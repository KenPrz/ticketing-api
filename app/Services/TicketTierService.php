<?php

namespace App\Services;

use App\Enums\TicketType;
use App\Models\Event;
use App\Models\EventTicketTier;
use App\Models\Seat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketTierService
{
    /**
     * The ticket tier model instance.
     *
     * @var EventTicketTier
     */
    protected $eventTicketTier;

    /**
     * The seat model instance.
     *
     * @var Seat
     */
    protected $seat;

    /**
     * Construct the ticket tier service instance.
     *
     * @param  EventTicketTier  $eventTicketTier  The ticket tier model instance
     * @param  Seat  $seat  The seat model instance
     *
     * @return void
     */
    public function __construct(
        EventTicketTier $eventTicketTier,
        Seat $seat
    ) {
        $this->eventTicketTier = $eventTicketTier;
        $this->seat = $seat;
    }

    /**
     * Create ticket tiers and seats for an event.
     *
     * @param Event $event The event to create tickets for
     * @param array $tickets The ticket tier data
     *
     * @return array The created ticket tiers
     */
    public function createTicketsAndSeats(Event $event, array $tickets)
    {
        $ticketTiers = [];

        foreach ($tickets as $ticketData) {
            // Check if $ticketData is an object or an array and access properties accordingly
            $tierName = is_object($ticketData) ? $ticketData->tier_name : $ticketData['tier_name'];
            $price = is_object($ticketData) ? $ticketData->price : $ticketData['price'];
            $quantity = is_object($ticketData) ? $ticketData->quantity : $ticketData['quantity'];
            $ticketType = is_object($ticketData) ? $ticketData->ticket_type : $ticketData['ticket_type'];

            // Create the ticket tier
            $ticketTier = $event->ticketTiers()->create([
                'tier_name' => $tierName,
                'price' => $price,
                'quantity' => $quantity,
                'ticket_type' => $ticketType,
            ]);

            // Add seats if it's a seated ticket type
            $this->createSeats($ticketTier);

            $ticketTiers[] = $ticketTier;
        }

        return $ticketTiers;
    }

    /**
     * Create seats for a ticket tier.
     *
     * @param EventTicketTier $ticketTier The ticket tier to create seats for
     *
     * @return array The created seats
     */
    public function createSeats(EventTicketTier $ticketTier)
    {
        $seats = [];
        $quantity = $ticketTier->quantity;

        // Only create seats for seated ticket types
        if ($this->isSeatedTicketType($ticketTier->ticket_type)) {
            for ($i = 1; $i <= $quantity; $i++) {
                $seats[] = $ticketTier->seats()->create([
                    'seat_number' => $i,
                    'status' => 'available',
                ]);
            }
        }

        return $seats;
    }

    /**
     * Check if a ticket type is a seated type.
     *
     * @param string|TicketType $ticketType The ticket type to check
     *
     * @return bool True if the ticket type is seated
     */
    private function isSeatedTicketType($ticketType): bool
    {
        // Convert enum to string if needed
        $typeValue = $ticketType;
        
        // If it's an enum, get its value
        if ($ticketType instanceof TicketType) {
            $typeValue = $ticketType->value;
        }
        
        $standingTypes = ['GENERAL ADMISSION', 'FLOOR STANDING'];
        return !in_array($typeValue, $standingTypes);
    }

    /**
     * Delete all ticket tiers and associated data for an event.
     * 
     * @param Event $event The event whose ticket tiers should be deleted
     * @param bool $forceDeletion Whether to delete tiers with sold tickets (defaults to false)
     * 
     * @return bool True if deletion was successful
     * @throws \Exception If there are tickets already sold for any tier and forceDeletion is false
     */
    public function deleteTicketTiers(Event $event, bool $forceDeletion = false): bool
    {
        // Begin transaction to ensure data consistency
        DB::beginTransaction();
        
        try {
            // Check if any tickets have been sold for any tier
            if (!$forceDeletion) {
                foreach ($event->ticketTiers as $tier) {
                    if ($tier->tickets->count() > 0) {
                        // Tickets have been sold, cannot delete tiers
                        throw new \Exception('Cannot delete ticket tiers because tickets have already been sold.');
                    }
                }
            }
            
            // Delete all ticket tiers and related data
            foreach ($event->ticketTiers as $tier) {
                // Delete seating information if it exists
                if ($tier->seats && $tier->seats->count() > 0) {
                    foreach ($tier->seats as $seat) {
                        $seat->delete();
                    }
                }
                
                // For tiers with sold tickets that are being force deleted, 
                // we might need to keep records of these tickets or mark them cancelled
                if ($forceDeletion && $tier->tickets->count() > 0) {
                    Log::info("Force deleting ticket tier #{$tier->id} with {$tier->tickets->count()} sold tickets");
                    // Additional logic for handling sold tickets could go here
                }
                
                // Delete the tier itself
                $tier->delete();
            }
            
            // Commit the transaction
            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollBack();
            Log::error("Error deleting ticket tiers: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update ticket tiers for an event.
     * 
     * @param Event $event The event to update tickets for
     * @param array $tickets The new ticket tier data
     * @param bool $forceDeletion Whether to delete tiers with sold tickets (defaults to false)
     * 
     * @return array The updated ticket tiers
     * @throws \Exception If an error occurs during update
     */
    public function updateTicketTiers(Event $event, array $tickets, bool $forceDeletion = false): array
    {
        DB::beginTransaction();
        
        try {
            // First delete existing ticket tiers
            $this->deleteTicketTiers($event, $forceDeletion);
            
            // Then create new ticket tiers
            $result = $this->createTicketsAndSeats($event, $tickets);
            
            DB::commit();
            
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating ticket tiers: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Check if any ticket tiers have sold tickets.
     * 
     * @param Event $event The event to check
     * 
     * @return bool True if any tier has sold tickets
     */
    public function hasSoldTickets(Event $event): bool
    {
        foreach ($event->ticketTiers as $tier) {
            if ($tier->tickets->count() > 0) {
                return true;
            }
        }
        
        return false;
    }
}