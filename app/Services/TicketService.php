<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
     * Create a new ticket.
     *
     * @param array $data The ticket data
     *
     * @return Ticket The created ticket instance
     */
    public function createTicket(array $data): Ticket
    {
        return $this->ticket->create($data);
    }

    /**
     * Get a ticket by its ID.
     *
     * @param string $id The ID of the ticket to retrieve
     *
     * @throws ModelNotFoundException When ticket is not found
     *
     * @return Ticket The found ticket instance
     */
    public function getTicket(string $id): Ticket
    {
        return $this->ticket->findOrFail($id);
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
        $ticket->update(['user_id' => $userId]);

        return $ticket->fresh();
    }

    /**
     * Cancel a ticket.
     *
     * @param string $id The ID of the ticket to cancel
     *
     * @throws ModelNotFoundException When ticket is not found
     *
     * @return Ticket The cancelled ticket instance
     */
    public function cancelTicket(string $id): Ticket
    {
        $ticket = $this->getTicket($id);
        $ticket->update(['status' => 'cancelled']);

        return $ticket->fresh();
    }

    /**
     * Use a ticket.
     *
     * @param string $id The ID of the ticket to use
     *
     * @throws ModelNotFoundException When ticket is not found
     *
     * @return Ticket The used ticket instance
     */
    public function useTicket(string $id): Ticket
    {
        $ticket = $this->getTicket($id);
        $ticket->update(['status' => 'used']);

        return $ticket->fresh();
    }
}