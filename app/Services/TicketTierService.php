<?php
namespace App\Services;

use App\Enums\TicketType;
use App\Models\{
    Event,
    EventTicketTier,
    Seat,
};
use Illuminate\Support\Facades\DB;

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

    /**
     * Create ticket tiers for an event.
     * 
     * @param Event $event The event instance
     * @param array $data The ticket tier data
     * @return array Array of created EventTicketTier instances
     */
    public function createTicketsAndSeats(Event $event, array $data): array
    {
        // Start a transaction to ensure ticket and seat are created together
        return DB::transaction(function () use ($data, $event) {
            $createdTiers = [];
            foreach ($data as $tier){
                // Create the ticket tier
                $createdTiers[] = $this->eventTicketTier->create([
                    'event_id' => $event->id,
                    'tier_name' => $tier->tier_name,
                    'price' => $tier->price,
                    'quantity' => $tier->quantity,
                    'ticket_type' => $tier->ticket_type,
                ]);
            }

            // Get ticket prefixes from TicketType enum
            $ticketPrefixes = TicketType::ticketPrefix();

            // Create seats for each ticket tier
            foreach ($createdTiers as $tier) {
                $quantity = $tier->quantity;
                $section = $tier->ticket_type->value;
                $prefix = $ticketPrefixes[$section] ?? substr($section, 0, 3);
                $now = now();
                
                // Skip creating individual seats for standing areas
                if (
                    str_contains($section, TicketType::FLOOR_STANDING->value)
                    || str_contains($section, TicketType::GENERAL_ADMISSION->value)
                ) {
                    // For standing areas, just create placeholder entries
                    $seats = [];
                    
                    for ($i = 1; $i <= $quantity; $i++) {
                        $seats[] = [
                            'event_id' => $event->id,
                            'section' => $section,
                            'is_occupied' => false,
                            'row' => "{$prefix}",
                            'number' => "{$i}",
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                        
                        // Insert in chunks to avoid memory issues
                        if (count($seats) >= 1000) {
                            Seat::insert($seats);
                            $seats = [];
                        }
                    }
                    
                    // Insert any remaining seats
                    if (count($seats) > 0) {
                        Seat::insert($seats);
                    }
                } else {
                    // For seated areas, create a sensible layout
                    
                    // Calculate a reasonable number of seats per row
                    $seatsPerRow = min(max(10, ceil(sqrt($quantity))), 20);
                    
                    // Calculate number of rows needed
                    $rowCount = ceil($quantity / $seatsPerRow);
                    
                    // Generate rows using letters (A, B, C, ...)
                    $rowLetters = range('A', 'Z');
                    
                    // Create the seats
                    $seats = [];
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
                            
                            $seats[] = [
                                'event_id' => $event->id,
                                'row' => $rowName,
                                'number' => $seatNum,
                                'section' => $section,
                                'is_occupied' => false,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                            
                            // Insert in chunks to avoid memory issues
                            if (count($seats) >= 1000) {
                                Seat::insert($seats);
                                $seats = [];
                            }
                        }
                    }
                    
                    // Insert any remaining seats
                    if (count($seats) > 0) {
                        Seat::insert($seats);
                    }
                }
            }
            
            return $createdTiers;
        });
    }
}