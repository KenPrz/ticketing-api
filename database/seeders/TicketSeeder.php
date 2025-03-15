<?php

namespace Database\Seeders;

use App\Enums\TicketType;
use App\Enums\UserTypes;
use App\Models\Event;
use App\Models\User;
use Faker\Generator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
        $events = Event::all();
        $users = User::where('user_type', UserTypes::CLIENT->value)->get();

        $events->each(function ($event) use ($users) {
            $users->each(function ($user) use ($event) {
                $ticketType = $this->faker->randomElement(TicketType::cases());
                $event->tickets()->create([
                    'qr_code' => $this->generateQrDetails($event, $user),
                    'ticket_name' => "{$event->name} - {$user->name} - {$ticketType->value}",
                    'owner_id' => $user->id,
                    'ticket_type' => $ticketType->value,
                    'ticket_desc' => $this->faker->sentence,
                ]);
            });
        });
    }

    /**
     * Generate QR code details for a ticket.
     *
     * This method generates a string containing details about the event and the ticket owner,
     * which can be used to create a QR code for the ticket.
     *
     * @param Event $event The event for which the ticket is issued.
     * @param User $user The user who owns the ticket.
     *
     * @return string A string containing the event name, ticket owner name, event date, and a unique code.
     */
    private function generateQrDetails(Event $event, User $user): string
    {
        return "Event: {$event->name}
            \nTicket Owner: {$user->name}
            \nTicket Date: {$event->date}
            \nCode: {$event->id}-{$user->id}-" . uniqid();
    }
}
