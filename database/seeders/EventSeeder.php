<?php

namespace Database\Seeders;

use App\Enums\UserTypes;
use App\Models\{
    Event,
    User,
};
use Faker\Generator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
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
        $users = User::where('user_type', UserTypes::ORGANIZER->value)->get();
        $users->each(function ($user) {
            Event::create([
                'event_name' => $this->faker->sentence(rand(2, 4)),
                'organizer_id' => $user->id,
                'event_date' => $this->faker->dateTimeBetween('now', '+1 year'),
                'event_description' => $this->faker->sentence(rand(10, 20)),
            ]);
        });
    }
}
