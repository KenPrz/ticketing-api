<?php

namespace Database\Seeders;

use App\Enums\PostVoteType;
use App\Enums\UserTypes;
use App\Models\Post;
use App\Models\User;
use Faker\Generator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostVoteSeeder extends Seeder
{
    /**
     * The Faker instance.
     *
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
        // Get total count of posts for the progress bar
        $totalPosts = Post::count();
        
        // Initialize progress bar if command is available
        $bar = null;
        if ($this->command) {
            $this->command->info('Creating votes for posts...');
            $bar = $this->command->getOutput()->createProgressBar($totalPosts);
            $bar->start();
        }

        Post::chunk(500, function ($posts) use ($bar) {
            foreach ($posts as $post) {
                $numVotes = rand(1, 20);
                $userIds = User::whereIn('user_type', UserTypes::exposedUserTypes())
                    ->inRandomOrder()
                    ->limit($numVotes)
                    ->pluck('id')
                    ->toArray();

                for ($i = 0; $i < $numVotes; $i++) {
                    $voteType = $this->faker->boolean(75) 
                        ? PostVoteType::UPVOTE
                        : PostVoteType::DOWNVOTE;
                    $post->votes()->create([
                        'user_id' => $userIds[$i],
                        'vote_type' => $voteType,
                    ]);
                }
                
                // Advance the progress bar for each post processed
                if ($bar) {
                    $bar->advance();
                }
            }
        });
        
        // Finish the progress bar and display completion message
        if ($bar) {
            $bar->finish();
            $this->command->info("\nPost vote seeding completed successfully!");
        }
    }
}