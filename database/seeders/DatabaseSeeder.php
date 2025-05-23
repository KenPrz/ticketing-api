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

        // create the tester user
        User::create([
            'name' => env('TESTER_NAME', 'John Doe'),
            'mobile' => env('TESTER_MOBILE', '+639123456789'),
            'email' => env('TESTER_EMAIL','johndoe@email.com'),
            'user_type' => env('TESTER_USER_TYPE', UserTypes::CLIENT->value),
            'password' => bcrypt(env('TESTER_PASSWORD', 'password')),
            'recent_latitude' => config('constants.default_coordinates.latitude'),
            'recent_longitude' => config('constants.default_coordinates.longitude'),
        ]);

        User::create([
            'Name' => 'Customer Support',
            'mobile' => '+639123456788',
            'email' => 'support@qphoria.com',
            'user_type' => UserTypes::ORGANIZER->value,
            'password' => bcrypt('password'),
            'recent_latitude' => config('constants.default_coordinates.latitude'),
            'recent_longitude' => config('constants.default_coordinates.longitude'),
        ]);

        $this->call([
            UserSeeder::class,
            EventSeeder::class,
            EventTicketTierSeeder::class,
            SeatSeeder::class,
            TicketSeeder::class,
            EventBookmarkSeeder::class,
            FriendsSeeder::class,
            VoucherSeeder::class,
            PostSeeder::class,
            PostVoteSeeder::class,
            ChatSeeder::class,
        ]);
    }
}