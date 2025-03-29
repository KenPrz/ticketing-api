<?php

namespace Database\Seeders;

use App\Enums\TicketType;
use App\Models\Event;
use App\Models\Seat;
use Illuminate\Database\Seeder;

class SeatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Event::with('ticketTiers')->get();
        $ticketPrefixes = TicketType::ticketPrefix();
        
        // Count total seats to create for progress bar
        $totalSeats = 0;
        foreach ($events as $event) {
            foreach ($event->ticketTiers as $tier) {
                $totalSeats += $tier->quantity;
            }
        }
        
        // Start progress bar
        $this->command->getOutput()->progressStart($totalSeats);
        
        foreach ($events as $event) {
            foreach ($event->ticketTiers as $tier) {
                $quantity = $tier->quantity;
                $section = $tier->tier_name;
                $prefix = $ticketPrefixes[$section] ?? substr($section, 0, 3);
                
                // Prepare for bulk insertion
                $seats = [];
                $now = now();
                
                // Skip creating individual seats for standing areas
                if (
                    str_contains($section, TicketType::FLOOR_STANDING->value)
                    || str_contains($section, TicketType::GENERAL_ADMISSION->value)
                ) {
                    // For standing areas, just create placeholder entries
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
                            $this->command->getOutput()->progressAdvance(count($seats));
                            $seats = [];
                        }
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
                                $this->command->getOutput()->progressAdvance(count($seats));
                                $seats = [];
                            }
                        }
                    }
                }
                
                // Insert any remaining seats
                if (count($seats) > 0) {
                    Seat::insert($seats);
                    $this->command->getOutput()->progressAdvance(count($seats));
                }
            }
        }
        
        // Finish progress bar
        $this->command->getOutput()->progressFinish();
        $this->command->info("Created {$totalSeats} seats for {$events->count()} events.");
    }
}