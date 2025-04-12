<?php

namespace App\Services;

use App\Enums\TicketTransferStatus;
use App\Enums\UserTypes;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class TransferService
{
    /**
     * Check if the email is valid for ticket transfer.
     *
     * @param  string  $email
     * @param  User  $currentUser
     * @return array
     */
    public function checkEmailValidity(string $email, User $currentUser): array
    {
        // Check if user is trying to transfer to themselves
        if ($currentUser->email === $email) {
            return [
                'valid' => false,
                'message' => 'You cannot transfer to yourself',
                'status' => 422
            ];
        }

        // Get recipient user and check if they are a client
        $recipient = User::where('email', $email)->first();
        if ($recipient->user_type !== UserTypes::CLIENT) {
            return [
                'valid' => false, 
                'message' => 'Invalid user',
                'status' => 403
            ];
        }

        // Verify email format (redundant with validation but keeping for completeness)
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'valid' => true,
                'status' => 200
            ];
        }

        return [
            'valid' => false,
            'status' => 422
        ];
    }

    /**
     * Initiate a ticket transfer to another user.
     *
     * @param  int  $ticketId
     * @param  string  $email
     * @param  User  $currentUser
     *
     * @return array
     */
    public function transferTicket(
        int $ticketId,
        string $email,
        User $currentUser,
    ): array {
        // Find the ticket and the user to transfer to
        $ticket = Ticket::find($ticketId);
        $userToTransfer = User::where('email', $email)->first();

        if (!$ticket || !$userToTransfer) {
            return [
                'success' => false,
                'message' => 'Ticket or user not found',
                'status' => 404
            ];
        }

        // Check if the ticket belongs to the authenticated user
        if ($ticket->owner_id !== $currentUser->id) {
            return [
                'success' => false,
                'message' => 'You do not own this ticket',
                'status' => 403
            ];
        }

        // Check if there's already a pending transfer for this ticket
        $pendingTransfer = $ticket->transferHistory()
            ->where('status', TicketTransferStatus::PENDING)
            ->first();

        if ($pendingTransfer) {
            return [
                'success' => false,
                'message' => 'There is already a pending transfer for this ticket',
                'status' => 422
            ];
        }

        // Create a transfer history record with PENDING status
        $transferHistory = $ticket->transferHistory()->create([
            'from_user_id' => $currentUser->id,
            'to_user_id' => $userToTransfer->id,
            'status' => TicketTransferStatus::PENDING,
        ]);

        // Send notification email to the recipient
        $userToTransfer->notify(new \App\Notifications\TicketTransferRequest($transferHistory));

        // Fire an event for the transfer request
        event(new \App\Events\TicketTransferRequested($transferHistory));

        return [
            'success' => true,
            'message' => 'Ticket transfer request sent successfully',
            'status' => 200
        ];
    }
    
    /**
     * Accept a ticket transfer.
     *
     * @param  int  $transferId
     * @return array
     */
    public function acceptTransfer(int $transferId): array
    {
        $transferHistory = \App\Models\TicketTransferHistory::find($transferId);
        
        if (!$transferHistory) {
            return [
                'success' => false,
                'message' => 'Transfer not found',
                'status' => 404
            ];
        }
        
        if ($transferHistory->status !== TicketTransferStatus::PENDING) {
            return [
                'success' => false,
                'message' => 'This transfer has already been processed',
                'status' => 422
            ];
        }
        
        $ticket = $transferHistory->ticket;
        
        // Update the ticket owner
        $ticket->owner_id = $transferHistory->to_user_id;
        $ticket->save();
        
        // Update the transfer history
        $transferHistory->status = TicketTransferStatus::TRANSFERRED;
        $transferHistory->transfer_date = now();
        $transferHistory->save();
        
        // Notify the original owner that the transfer was accepted
        $fromUser = $transferHistory->fromUser;
        $fromUser->notify(new \App\Notifications\TicketTransferAccepted($transferHistory));
        
        // Fire an event for the accepted transfer
        event(new \App\Events\TicketTransferAccepted($transferHistory));
        
        return [
            'success' => true,
            'message' => 'Ticket transfer accepted successfully',
            'status' => 200
        ];
    }
    
    /**
     * Reject a ticket transfer.
     *
     * @param  int  $transferId
     * @return array
     */
    public function rejectTransfer(int $transferId): array
    {
        $transferHistory = \App\Models\TicketTransferHistory::find($transferId);
        
        if (!$transferHistory) {
            return [
                'success' => false,
                'message' => 'Transfer not found',
                'status' => 404
            ];
        }
        
        if ($transferHistory->status !== TicketTransferStatus::PENDING) {
            return [
                'success' => false,
                'message' => 'This transfer has already been processed',
                'status' => 422
            ];
        }

        // Update the transfer history
        $transferHistory->status = TicketTransferStatus::REJECTED;
        $transferHistory->save();
        
        // Notify the original owner that the transfer was rejected
        $fromUser = $transferHistory->fromUser;
        $fromUser->notify(new \App\Notifications\TicketTransferRejected($transferHistory));
        
        // Fire an event for the rejected transfer
        event(new \App\Events\TicketTransferRejected($transferHistory));
        
        return [
            'success' => true,
            'message' => 'Ticket transfer rejected successfully',
            'status' => 200
        ];
    }

    /**
     * Cancel a ticket transfer.
     *
     * @param  int  $transferId
     * @return array
     */
    public function cancelTransfer(
        int $transferId,
        User $currentUser,
    ): array {
        $transferHistory = \App\Models\TicketTransferHistory::find($transferId);

        if (!$transferHistory) {
            return [
                'success' => false,
                'message' => 'Transfer not found',
                'status' => 404
            ];
        }

        // Check if the ticket belongs to the authenticated user
        if ($transferHistory->ticket->owner_id !== $currentUser->id) {
            return [
                'success' => false,
                'message' => 'You do not own this ticket',
                'status' => 403
            ];
        }

        // Check if the current user is the one who initiated the transfer.
        if ($transferHistory->from_user_id !== $currentUser->id) {
            return [
                'success' => false,
                'message' => 'You are not authorized to cancel this transfer',
                'status' => 403
            ];
        }

        if ($transferHistory->status !== TicketTransferStatus::PENDING) {
            return [
                'success' => false,
                'message' => 'This transfer cannot be cancelled',
                'status' => 422
            ];
        }

        // Update the transfer history
        $transferHistory->status = TicketTransferStatus::CANCELLED;
        $transferHistory->save();
        
        // Notify the recipient that the transfer was cancelled
        $toUser = $transferHistory->toUser;
        $toUser->notify(new \App\Notifications\TicketTransferCancelled($transferHistory));
        
        // Fire an event for the cancelled transfer
        event(new \App\Events\TicketTransferCancelled($transferHistory));

        return [
            'success' => true,
            'message' => 'Ticket transfer cancelled successfully',
            'status' => 200
        ];
    }
}