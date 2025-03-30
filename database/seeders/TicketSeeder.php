<?php

namespace Database\Seeders;

use App\Enums\TicketType;
use App\Enums\UserTypes;
use App\Models\Event;
use App\Models\EventTicketTier;
use App\Models\Purchase;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\User;
use Faker\Generator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\Console\Helper\ProgressBar;

class TicketSeeder extends Seeder
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
            $this->handleTestClient();
            $events = Event::with('ticketTiers')->get();
            $allUsers = User::where('user_type', UserTypes::CLIENT->value)->get();

            // Get command output for progress bar
            $output = $this->command->getOutput();
            $output->writeln('<info>Generating tickets for events...</info>');

            $ticketCount = 0;
            $purchaseCount = 0;
            $seatCount = 0;
            $totalOperations = 0;

            // Calculate total operations for progress bar
            foreach ($events as $event) {
                $ticketTiers = $event->ticketTiers;
                if ($ticketTiers->isEmpty()) {
                    continue;
                }
                $totalTickets = $ticketTiers->sum('quantity');
                $totalOperations += $totalTickets;
            }

            // Create progress bar
            $progressBar = new ProgressBar($output, $totalOperations);
            $progressBar->setFormat(
                "%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s% \n%message%"
            );
            $progressBar->setMessage('Starting ticket generation...');
            $progressBar->start();

            $events->each(function ($event) use ($allUsers, $progressBar, &$ticketCount, &$purchaseCount, &$seatCount) {
                // Get ticket tiers for this event
                $ticketTiers = $event->ticketTiers;

                if ($ticketTiers->isEmpty()) {
                    $progressBar->setMessage("Skipping Event ID {$event->id} (no ticket tiers)");
                    return; 
                }

                // Calculate total tickets available for this event
                $totalTicketsForEvent = $ticketTiers->sum('quantity');

                // Track available quantities for each tier
                $tierQuantities = [];
                foreach ($ticketTiers as $tier) {
                    $tierQuantities[$tier->id] = $tier->quantity;
                }

                $randomReduction = rand(0, floor($totalTicketsForEvent * 0.25));
                $selectedUsers = $allUsers->shuffle()->take($totalTicketsForEvent - $randomReduction);            

                // Give each user exactly one ticket
                foreach ($selectedUsers as $user) {
                    $progressBar->setMessage("Processing: Event #{$event->id} '{$event->name}' - User #{$user->id} '{$user->name}'");

                    // Skip if no more tickets available in any tier
                    if (array_sum($tierQuantities) <= 0) {
                        $progressBar->advance();
                        continue;
                    }
                    
                    // Create a purchase and ticket for this user
                    DB::transaction(function () use (
                        $user, $event, $ticketTiers, &$tierQuantities, 
                        $progressBar, &$ticketCount, &$purchaseCount, &$seatCount
                    ) {
                        // Create purchase record
                        $purchase = Purchase::create([
                            'placeholder_for_transaction_handler' => 'TXN_' . uniqid().Str::random(32),
                            'event_id' => $event->id,
                            'purchased_by' => $user->id,
                        ]);
                        $purchaseCount++;

                        // Find available tiers (those with remaining quantity)
                        $availableTiers = array_filter($tierQuantities, function($qty) {
                            return $qty > 0;
                        });

                        if (empty($availableTiers)) {
                            return;
                        }

                        // Randomly select a tier ID from available tiers
                        $tierIds = array_keys($availableTiers);
                        $selectedTierId = $tierIds[array_rand($tierIds)];
                        $selectedTier = $ticketTiers->firstWhere('id', $selectedTierId);
                        
                        // Decrement the available quantity for this tier
                        $tierQuantities[$selectedTierId]--;

                        // Create ticket
                        $ticket = Ticket::create([
                            'qr_code' => $this->generateQrDetails($event, $user),
                            'ticket_name' => "{$event->name} - {$user->name} - {$selectedTier->tier_name}",
                            'event_id' => $event->id,
                            'owner_id' => $user->id,
                            'ticket_tier_id' => $selectedTier->id,
                            'purchase_id' => $purchase->id,
                            'ticket_type' => $selectedTier->ticket_type,
                            'ticket_desc' => "Ticket for {$selectedTier->tier_name} section",
                            'is_used' => false,
                            'used_on' => null,
                        ]);
                        $ticketCount++;
                        
                        // Create seat for this ticket
                        $this->createSeatForTicket($ticket, $event);
                        $seatCount++;
                    });
                    
                    $progressBar->advance();
                }
            });

            $progressBar->finish();
            $output->writeln('');
            $output->writeln("<info>Ticket generation complete!</info>");
            $output->writeln("<info>Generated {$purchaseCount} purchases, {$ticketCount} tickets, and {$seatCount} seats.</info>");
        }

    /**
     * Create a seat for a ticket based on ticket type.
     *
     * @param Ticket $ticket The ticket to create a seat for
     * @param Event $event The event the ticket belongs to
     * @return Seat|null The created seat or null if no seat is available
     */
    private function createSeatForTicket(
        Ticket $ticket,
        Event $event
    ): Seat | null {
        // Find an available seat that matches the ticket's section
        $seat = Seat::where('event_id', $event->id)
            ->where('section', $ticket->ticket_type)
            ->where('is_occupied', false)
            ->whereNull('ticket_id')
            ->first();

        if (!$seat) {
            return null;
        }

        // Update the seat to assign it to the ticket and mark as occupied
        $seat->update([
            'ticket_id' => $ticket->id,
            'is_occupied' => true
        ]);

        return $seat;
    }

    /**
     * Generate QR code details for a ticket.
     *
     * @param Event $event The event for which the ticket is issued.
     * @param User $user The user who owns the ticket.
     *
     * @return string A string containing the event name, ticket owner name, event date, and a unique code.
     */
    private function generateQrDetails(Event $event, User $user): string
    {
        return uniqid()."--".Str::random(32);
    }

    /**
     * Make sure the test client has tickets for all events.
     *
     * @return void
     */
    private function handleTestClient() {
        $client = User::where('id', 2)->first();

        if (!$client) {
            return;
        }

        $events = Event::all();

        $events->each(function ($event) use ($client) {
            $ticket = Ticket::where('event_id', $event->id)
                ->where('owner_id', $client->id)
                ->first();

            if (!$ticket) {
                $tier = EventTicketTier::where('event_id', $event->id)->first();
                if ($tier) {
                    $purchase = Purchase::create([
                        'placeholder_for_transaction_handler' => 'TXN_' . uniqid().Str::random(32),
                        'event_id' => $event->id,
                        'purchased_by' => $client->id,
                    ]);

                    $ticket = Ticket::create([
                        'qr_code' => $this->generateQrDetails($event, $client),
                        'ticket_name' => "{$event->name} - {$client->name} - {$tier->tier_name}",
                        'event_id' => $event->id,
                        'owner_id' => $client->id,
                        'ticket_tier_id' => $tier->id,
                        'purchase_id' => $purchase->id,
                        'ticket_type' => $tier->ticket_type,
                        'ticket_desc' => "Ticket for {$tier->tier_name} section",
                        'is_used' => false,
                        'used_on' => null,
                    ]);

                    $this->createSeatForTicket($ticket, $event);
                }
            }
        });
    }
}