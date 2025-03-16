<?php

namespace Database\Seeders;

use App\Enums\UserTypes;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // create the admin user
        User::create([
            'name' => 'Admin',
            'mobile' => '+639123456779',
            'email' => 'admin@email.com',
            'user_type' => UserTypes::ADMIN->value,
            'password' => bcrypt('password'),
        ]);

        // create the user
        User::create([
            'name' => 'User',
            'mobile' => '+639123456799',
            'email' => 'user@email.com',
            'user_type' => UserTypes::CLIENT->value,
            'password' => bcrypt('password'),
        ]);

        // create an organizer
        User::create([
            'name' => 'Organizer',
            'mobile' => '+639123456780',
            'email' => 'organizer@email.com',
            'user_type' => UserTypes::ORGANIZER->value,
            'password' => bcrypt('password'),
        ]);

        $this->call([
            UserSeeder::class,
            EventSeeder::class,
            EventTicketTierSeeder::class,
            TicketSeeder::class,
        ]);
    }
}