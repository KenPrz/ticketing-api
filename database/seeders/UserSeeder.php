<?php

namespace Database\Seeders;

use App\Enums\UserTypes;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Arr;

class UserSeeder extends Seeder
{
    /**
     * Prevent Eloquent from firing model events.
     */
    use WithoutModelEvents;

    /**
     * The Faker instance.
     */
    protected $faker;

    /**
     * Construct the fajer instance.
     */
    public function __construct(Faker $faker)
    {
        $this->faker = $faker;
    }

    /**
     * The user counts.
     * 
     * @var array<int>
     */
    protected const USER_COUNTS = [
        'client' => 100,
        'admin' => 5,
        'organizer' => 10,
    ];

    /**
     * The default password.
     * 
     * @var string
     */
    protected const DEFAULT_PASSWORD = 'password';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding clients...');
        // Create the specified number of clients
        User::factory()
            ->count(self::USER_COUNTS['client'])
            ->create(['user_type' => UserTypes::CLIENT->value]);

        $this->command->info('Seeding admins...');
        // Create the specified number of admins
        User::factory()
            ->count(self::USER_COUNTS['admin'])
            ->create(['user_type' => UserTypes::ADMIN->value]);
    
        $this->command->info('Seeding organizers...');
        // Create the specified number of organizers
        User::factory()
            ->count(self::USER_COUNTS['organizer'])
            ->create(['user_type' => UserTypes::ORGANIZER->value]);
    
        // Output a success message once the users have been seeded
        $this->command->info('Users seeded successfully.');
    }
}
