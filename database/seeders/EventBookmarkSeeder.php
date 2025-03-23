<?php

namespace Database\Seeders;

use App\Enums\UserTypes;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventBookmarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('user_type', UserTypes::CLIENT)->get();
        $events = Event::all();

        // attach random events to users
        $users->each(function (User $user) use ($events) {
            $user->eventBookmarks()->attach(
                $events->random(rand(1, 5))->pluck('id')->toArray()
            );
        });
    }
}
