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
            $this->createSeats($ticketTier, $event->id);

            $ticketTiers[] = $ticketTier;
        }

        return $ticketTiers;
    }

    /**
     * Create seats for a ticket tier.
     *
     * @param EventTicketTier $ticketTier The ticket tier to create seats for
     * @param int $eventId The event ID
     *
     * @return array The created seats
     */
    public function createSeats(EventTicketTier $ticketTier, $eventId)
    {
        $seats = [];
        $quantity = $ticketTier->quantity;
        
        // Get the ticket type - it's an enum instance in your system
        $ticketType = $ticketTier->ticket_type;
        
        // If stored as a string, convert to enum (if needed)
        if (is_string($ticketType)) {
            $ticketType = TicketType::from($ticketType);
        }
        
        // Get the section name (the string value from the enum)
        $section = $ticketType->value;
        
        // Get the appropriate prefix from the enum's ticketPrefix method
        $ticketPrefixes = TicketType::ticketPrefix();
        $prefix = $ticketPrefixes[$section] ?? substr($section, 0, 3);
        
        $now = now();
        
        // Check if this is a standing area
        $isStandingArea = $ticketType === TicketType::FLOOR_STANDING || 
                        $ticketType === TicketType::GENERAL_ADMISSION;
        
        if ($isStandingArea) {
            // For standing areas, create placeholder entries
            for ($i = 1; $i <= $quantity; $i++) {
                $seats[] = $ticketTier->seats()->create([
                    'event_id' => $eventId,
                    'section' => $section,
                    'row' => $prefix,
                    'number' => $i,
                    'status' => 'available',
                ]);
                
                // If you need to insert in bulk for performance, you could collect the data
                // and use Seat::insert() as shown in the original code
            }
        } else {
            // For seated areas, create a layout with rows
            
            // Calculate a reasonable number of seats per row
            $seatsPerRow = min(max(10, ceil(sqrt($quantity))), 20);
            
            // Calculate number of rows needed
            $rowCount = ceil($quantity / $seatsPerRow);
            
            // Generate rows using letters (A, B, C, ...)
            $rowLetters = range('A', 'Z');
            
            // Create the seats
            $seatCount = 0;
            
            for ($rowIndex = 0; $rowIndex < $rowCount; $rowIndex++) {
                // If we need more than 26 rows, we'll use AA, AB, etc.
                $rowLetter = $rowIndex < 26 ? $rowLetters[$rowIndex] : 
                    $rowLetters[floor($rowIndex / 26) - 1] . $rowLetters[$rowIndex % 26];
                
                // Add the prefix to ensure uniqueness
                $rowName = "{$prefix}-{$rowLetter}";
                
                for ($seatNum = 1; $seatNum <= $seatsPerRow; $seatNum++) {
                    // Stop if we've created enough seats
                    if (++$seatCount > $quantity) {
                        break;
                    }
                    
                    $seats[] = $ticketTier->seats()->create([
                        'event_id' => $eventId,
                        'section' => $section,
                        'row' => $rowName,
                        'seat_number' => $seatNum,
                        'status' => 'available',
                    ]);
                }
            }
        }
        
        return $seats;
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
     * Fetch all seats associated with a specific ticket tier.
     * 
     * @param string $ticketTierId The ID of the ticket tier
     * 
     * @return \Illuminate\Database\Eloquent\Collection The seats associated with the ticket tier
     */
    public function fetchSeatsByTicketTierID(string $ticketTierId)
    {
        return $this->eventTicketTier
            ->findOrFail($ticketTierId)
            ->seatsByTicketTier;
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