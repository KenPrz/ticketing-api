<?php
namespace App\Services;

use App\Models\{
    EventTicketTier,
    Seat,
};

class TicketTierService 
{
    /**
     * The ticket model instance.
     *
     * @var EventTicketTier
     */
    protected $eventTicketTier;

    /**
     * Construct the EventTicketTier service instance.
     *
     * @param EventTicketTier $eventTicketTier The EventTicketTier model instance
     */
    public function __construct(EventTicketTier $eventTicketTier)
    {
        $this->eventTicketTier = $eventTicketTier;
    }

    /**
     * Get all seats for the authenticated user.
     *
     * @param int $ticketTierId The ticketTierId tier ID
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Seat>
     */
    public function fetchSeatsByTicketTierID(string $ticketTierId)
    {
        return $this->eventTicketTier
            ->findOrFail($ticketTierId)
            ->seatsByTicketTier;
    }
}