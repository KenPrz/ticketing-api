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
            'recent_latitude' => config('constants.default_coordinates.latitude'),
            'recent_longitude' => config('constants.default_coordinates.longitude'),
        ]);

        // create the user
        User::create([
            'name' => 'User',
            'mobile' => '+639123456799',
            'email' => 'user@email.com',
            'user_type' => UserTypes::CLIENT->value,
            'password' => bcrypt('password'),
            'recent_latitude' => config('constants.default_coordinates.latitude'),
            'recent_longitude' => config('constants.default_coordinates.longitude'),
        ]);

        // create an organizer
        User::create([
            'name' => 'Organizer',
            'mobile' => '+639123456780',
            'email' => 'organizer@email.com',
            'user_type' => UserTypes::ORGANIZER->value,
            'password' => bcrypt('password'),
            'recent_latitude' => config('constants.default_coordinates.latitude'),
            'recent_longitude' => config('constants.default_coordinates.longitude'),
        ]);

        $this->call([
            UserSeeder::class,
            EventSeeder::class,
            EventTicketTierSeeder::class,
            TicketSeeder::class,
            EventBookmarkSeeder::class,
        ]);
    }
}