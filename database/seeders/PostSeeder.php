<?php

namespace Database\Seeders;

use App\Enums\PostContext;
use App\Enums\UserTypes;
use App\Models\User;
use Faker\Generator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
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
        // Get total count of users for the progress bar
        $totalUsers = User::whereIn('user_type', UserTypes::exposedUserTypes())->count();
        
        // Initialize progress bar if command is available
        $bar = null;
        if ($this->command) {
            $this->command->info('Creating posts for users...');
            $bar = $this->command->getOutput()->createProgressBar($totalUsers);
            $bar->start();
        }

        User::whereIn('user_type', UserTypes::exposedUserTypes())
            ->with(['tickets' => function ($query) {
                $query->where('is_used', false);
            }])->chunk(500, function ($users) use ($bar) {
                foreach ($users as $user) {
                    $numPosts = rand(1, 3);
                    for ($i = 0; $i < $numPosts; $i++) {
                        $postContextType = $this->faker->randomElement(PostContext::list());
                        $selectedTicket = $this->faker->randomElement(
                            $user->tickets()->with('ticketTier')->get()->toArray()
                        );
                        if (empty($selectedTicket)) {
                            continue;
                        }
                        if ($postContextType === PostContext::SELL) {
                            $user->posts()->create([
                                'content' => $this->faker->sentence(),
                                'event_id' => $selectedTicket['event_id'],
                                'ticket_id' => $selectedTicket['id'],
                                'post_context' => $postContextType,
                                'price' => ($selectedTicket['ticket_tier']['price'] - $selectedTicket['ticket_tier']['price'] * 0.1),
                            ]);
                        } else if ($postContextType === PostContext::EXPERIENCE) {
                            $user->posts()->create([
                                'content' => $this->faker->sentence(),
                                'event_id' => $selectedTicket['event_id'],
                                'ticket_id' => null,
                                'post_context' => $postContextType,
                                'price' => null
                            ]);
                        } else if ($postContextType === PostContext::NORMAL) {
                            $user->posts()->create([
                                'content' => $this->faker->sentence(),
                                'event_id' => null,
                                'ticket_id' => null,
                                'price' => null,
                                'post_context' => $postContextType,
                            ]);
                        }
                    }
                    
                    // Advance the progress bar for each user processed
                    if ($bar) {
                        $bar->advance();
                    }
                }
            });
            
        // Finish the progress bar and display completion message
        if ($bar) {
            $bar->finish();
            $this->command->info("\nPost seeding completed successfully!");
        }
    }
}