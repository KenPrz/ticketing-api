<?php
namespace App\Services;

use App\Models\Seat;

class SeatService 
{
    /**
     * The ticket model instance.
     *
     * @var Seat
     */
    protected $seat;

    /**
     * Construct the ticket service instance.
     *
     * @param Seat $ticket The ticket model instance
     */
    public function __construct(Seat $seat)
    {
        $this->seat = $seat;
    }

    /**
     * Get all seats for the authenticated user.
     *
     * @param int $eventId The event ID
     * @param int $ticketTierId The ticket tier ID
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Seat>
     */
    public function fetchSeatsByEventId(string $eventId)
    {
        return $this->seat
            ->where('event_id', $eventId)
            ->get();
    }
}