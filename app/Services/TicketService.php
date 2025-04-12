<?php

namespace App\Services;

use App\Models\{
    Ticket,
    Seat,
    User,
};
use Illuminate\Database\Eloquent\{
    Collection,
    ModelNotFoundException,
};
use Illuminate\Support\Facades\DB;
class TicketService 
{
    /**
     * The ticket model instance.
     *
     * @var Ticket
     */
    protected $ticket;

    /**
     * Construct the ticket service instance.
     *
     * @param Ticket $ticket The ticket model instance
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get all tickets for the authenticated user.
     *
     * @param \App\Models\User $user
     * @return Collection<int, Ticket>
     */
    public function getMyTickets(User $user)
    {
        return $this->ticket->where('owner_id', $user->id)
            ->with(['event', 'ticketTier', 'purchase', 'seat', 'transferStatus'])
            ->get();
    }

    /**
     * Create a new ticket with related seat.
     *
     * @param array $data The ticket data
     *
     * @return Ticket The created ticket instance with seat relationship
     */
    public function createTicket(array $data): Ticket
    {
        // Start a transaction to ensure ticket and seat are created together
        return DB::transaction(function () use ($data) {
            // Create the ticket
            $ticket = $this->ticket->create($data);
            
            // Create an associated seat if seat data is provided
            if (isset($data['seat'])) {
                $seatData = $data['seat'];
                $seatData['ticket_id'] = $ticket->id;
                $seatData['event_id'] = $ticket->event_id;
                $seat = Seat::create($seatData);

                // Load the seat relationship
                $ticket->load('seat');
            }
            
            return $ticket;
        });
    }

    /**
     * Get a ticket by its ID with related data.
     *
     * @param string $id The ID of the ticket to retrieve
     * @param array $relations Relations to eager load
     *
     * @throws ModelNotFoundException When ticket is not found
     *
     * @return Ticket The found ticket instance with loaded relationships
     */
    public function getTicket(string $id, array $relations = ['event', 'ticketTier', 'owner', 'purchase.purchaser', 'seat', 'transferStatus']): Ticket
    {
        return $this->ticket->with($relations)->findOrFail($id);
    }

    /**
     * Update a ticket with the given data.
     *
     * @param string $id The ID of the ticket to update
     * @param array $data The data to update the ticket with
     *
     * @throws ModelNotFoundException When ticket is not found
     *
     * @return Ticket The updated ticket instance
     */
    public function updateTicket(string $id, array $data): Ticket
    {
        $ticket = $this->getTicket($id);
        $ticket->update($data);

        return $ticket->fresh();
    }

    /**
     * Delete a ticket by its ID.
     *
     * @param string $id The ID of the ticket to delete
     *
     * @throws ModelNotFoundException When ticket is not found
     *
     * @return bool True if deletion was successful
     */
    public function deleteTicket(string $id): bool
    {
        $ticket = $this->getTicket($id);
        return $ticket->delete();
    }

    /**
     * Transfer a ticket to another user.
     *
     * @param string $id The ID of the ticket to transfer
     * @param string $userId The ID of the user to transfer the ticket to
     *
     * @throws ModelNotFoundException When ticket is not found
     *
     * @return Ticket The transferred ticket instance
     */
    public function transferTicket(string $id, string $userId): Ticket
    {
        $ticket = $this->getTicket($id);
        $ticket->update(['owner_id' => $userId]);

        return $ticket->fresh(['owner']);
    }

    /**
     * Mark a ticket as used.
     *
     * @param string $id The ID of the ticket to mark as used
     *
     * @throws ModelNotFoundException When ticket is not found
     *
     * @return Ticket The used ticket instance
     */
    public function checkInTicket(string $id): Ticket
    {
        $ticket = $this->getTicket($id);
        
        if ($ticket->is_used) {
            throw new \Exception('Ticket has already been used.');
        }
        
        $ticket->update([
            'is_used' => true,
            'used_on' => now(),
        ]);
        
        // If this ticket has a seat, mark it as occupied
        if ($ticket->seat) {
            $ticket->seat->update(['is_occupied' => true]);
        }

        return $ticket->fresh(['seat']);
    }
    
    /**
     * Get all tickets for a specific event.
     *
     * @param string $eventId The ID of the event
     *
     * @return Collection Collection of tickets for the event
     */
    public function getEventTickets(string $eventId): Collection
    {
        return $this->ticket->where('event_id', $eventId)
            ->with(['ticketTier', 'owner', 'seat'])
            ->get();
    }
    
    /**
     * Get all tickets for a specific user.
     *
     * @param string $userId The ID of the user
     *
     * @return Collection Collection of tickets owned by the user
     */
    public function getUserTickets(string $userId): Collection
    {
        return $this->ticket->where('owner_id', $userId)
            ->with(['event', 'ticketTier', 'purchase', 'seat'])
            ->get();
    }
    
    /**
     * Get all tickets for a specific purchase.
     *
     * @param string $purchaseId The ID of the purchase
     *
     * @return Collection Collection of tickets in the purchase
     */
    public function getPurchaseTickets(string $purchaseId): Collection
    {
        return $this->ticket->where('purchase_id', $purchaseId)
            ->with(['event', 'ticketTier', 'owner', 'seat'])
            ->get();
    }

    /**
     * Mark a ticket as used.
     *
     * @param string $id The ID of the ticket to mark as used
     *
     * @throws ModelNotFoundException When ticket is not found
     *
     * @return Ticket The updated ticket instance
     */
    public function markTicketAsUsed(string $id)
    {
        $ticket = $this->getTicket($id);
        $ticket->update(['is_used' => true]);

        return $ticket->fresh();
    }
}