<?php

namespace App\Http\Controllers;

use App\Enums\UserTypes;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    /**
     * Check if the email is valid for transfer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkTransferEmailValidity(Request $request)
    {
        $request->validate([
            'email' => 'required|email|string|max:255|exists:users,email',
        ]);

        if ($request->user()->email === $request->email) {
            return response()->json(['valid' => false, 'message' => 'You cannot transfer to yourself'], 422);
        }

        $recipient = User::where('email', $request->email)->first();
        if ($recipient->user_type !== UserTypes::CLIENT) {
            return response()->json(['valid' => false, 'message' => 'invalid user'], 403);
        }

        // Check if the email is valid
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['valid' => true]);
        }

        return response()->json(['valid' => false], 422);
    }

    /**
     * Transfer a ticket to another user. 
     * sorry for writing this in the controller deadline is approaching and I need to get this done.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transferTicket(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'email' => 'required|email|string|max:255|exists:users,email',
        ]);

        // Find the ticket and the user to transfer to
        $ticket = Ticket::find($request->ticket_id);
        $userToTransfer = User::where('email', $request->email)->first();
        if (!$ticket || !$userToTransfer) {
            return response()->json(['message' => 'Ticket or user not found'], 404);
        }

        // Check if the ticket belongs to the authenticated user
        if ($ticket->owner_id !== $request->user()->id) {
            return response()->json(['message' => 'You do not own this ticket'], 403);
        }

        // Transfer the ticket
        $ticket->owner_id = $userToTransfer->id;
        $ticket->save();

        return response()->json(['message' => 'Ticket transferred successfully']);
    }
}
