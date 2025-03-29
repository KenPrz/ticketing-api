<?php

namespace Database\Seeders;

use App\Enums\TicketType;
use App\Models\Event;
use Faker\Generator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventTicketTierSeeder extends Seeder
{
    /**
     * The Faker instance.
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * Construct The faker instance.
     * 
     * @param \Faker\Generator $faker
     * 
     * @return void
     */
    public function __construct(Generator $faker)
    {
        $this->faker = $faker;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Event::all();
        $tiers = array_reverse(TicketType::cases());
        $seatingCapacities = TicketType::seatingCapacity();

        $events->each(function ($event) use ($tiers, $seatingCapacities) {
            // Start with a base price in 500s (1500, 2000, 2500, 3000, or 3500)
            $basePrice = 500 * rand(3, 7); 
    
            foreach ($tiers as $tier) {
                // Get the seating capacity for this ticket type using the string value as key
                $quantity = $seatingCapacities[$tier->value] ?? 0;
                
                // Create ticket tier with calculated price and proper quantity
                $event->ticketTiers()->create([
                    'tier_name' => $tier->value,
                    'ticket_type' => $tier,
                    'ticket_desc' => "This is a {$tier->value} ticket tier for the event: {$event->name}",
                    'price' => $basePrice,
                    'quantity' => $quantity,
                ]);

                // Calculate a random increment in 500s (500, 1000, 1500, 2000, 2500)
                $increment = 500 * rand(1, 5);

                // Increment the base price for the next tier
                $basePrice += $increment;
            }
        });
    }
}