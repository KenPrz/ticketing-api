<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\User;
use App\Models\UserFriend;
use App\Enums\FriendStatus;
use App\Enums\UserTypes;

class FriendsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Count total users for progress bar
        $totalUsers = User::count();
        $this->command->info("Creating friend relationships for {$totalUsers} users...");
        
        // Create progress bar
        $bar = $this->command->getOutput()->createProgressBar($totalUsers);
        $bar->start();
        
        // Use DB transactions for faster processing
        DB::transaction(function () use ($bar) {
            // Process users in chunks to avoid memory issues
            User::query()->chunkById(500, function ($users) use ($bar) {
                $friendships = [];
                $now = Carbon::now();
                $processedPairs = [];
                
                foreach ($users as $user) {
                    // Get 5 random users that aren't the current user
                    // Increased from 3 to 5 to create more relationship variety
                    $potentialFriends = User::where('id', '!=', $user->id)
                        ->inRandomOrder()
                        ->limit(5)
                        ->get(['id']);
                    
                    foreach ($potentialFriends as $friend) {
                        // Create a unique pair identifier to avoid duplicates
                        $pairKey1 = $user->id . '-' . $friend->id;
                        $pairKey2 = $friend->id . '-' . $user->id;
                        
                        // Skip if this pair has already been processed
                        if (isset($processedPairs[$pairKey1]) || isset($processedPairs[$pairKey2])) {
                            continue;
                        }
                        
                        // Mark this pair as processed
                        $processedPairs[$pairKey1] = true;
                        
                        // Randomly determine friendship status with weighted distribution
                        // 60% ACCEPTED, 20% PENDING, 15% REJECTED, 5% BLOCKED
                        $rand = mt_rand(1, 100);
                        
                        if ($rand <= 60) {
                            // ACCEPTED - bidirectional with same status
                            $status = FriendStatus::ACCEPTED;
                            
                            $friendships[] = [
                                'user_id' => $user->id,
                                'friend_id' => $friend->id,
                                'status' => $status->value,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                            
                            $friendships[] = [
                                'user_id' => $friend->id,
                                'friend_id' => $user->id,
                                'status' => $status->value,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                            
                        } elseif ($rand <= 80) {
                            // PENDING - A sent request to B, but B hasn't responded
                            $friendships[] = [
                                'user_id' => $user->id,
                                'friend_id' => $friend->id,
                                'status' => FriendStatus::PENDING->value,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                            
                        } elseif ($rand <= 95) {
                            // REJECTED - A sent request to B, but B rejected it
                            $friendships[] = [
                                'user_id' => $user->id,
                                'friend_id' => $friend->id,
                                'status' => FriendStatus::PENDING->value,
                                'created_at' => $now->copy()->subDays(rand(1, 7)),
                                'updated_at' => $now->copy()->subDays(rand(1, 7)),
                            ];
                            
                            $friendships[] = [
                                'user_id' => $friend->id,
                                'friend_id' => $user->id,
                                'status' => FriendStatus::REJECTED->value,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                            
                        } else {
                            // BLOCKED - A knows B but has blocked them
                            $friendships[] = [
                                'user_id' => $user->id,
                                'friend_id' => $friend->id,
                                'status' => FriendStatus::BLOCKED->value,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        }
                        
                        // Insert in batches of 1000 to avoid memory issues
                        if (count($friendships) >= 1000) {
                            UserFriend::insert($friendships);
                            $friendships = [];
                        }
                    }
                    
                    // Advance the progress bar for each user processed
                    $bar->advance();
                }
                
                // Insert any remaining friendships
                if (!empty($friendships)) {
                    UserFriend::insert($friendships);
                }
            });
        });
        
        // Finish the progress bar
        $bar->finish();
        
        // Add a new line after the progress bar
        $this->command->newLine();
        $this->command->info('Successfully seeded ' . UserFriend::count() . ' friend relationships.');
        
        // Show distribution of statuses
        $this->showStatusDistribution();
    }
    
    /**
     * Display the distribution of friend statuses after seeding.
     */
    private function showStatusDistribution(): void
    {
        $counts = [];
        foreach (FriendStatus::list() as $status) {
            $counts[$status->value] = UserFriend::where('status', $status->value)->count();
        }
        
        $this->command->newLine();
        $this->command->info('Friend Status Distribution:');
        $this->command->info('--------------------------------');
        $totalCount = array_sum($counts);
        foreach ($counts as $status => $count) {
            $percentage = $totalCount > 0 ? round(($count / $totalCount) * 100, 2) : 0;
            $this->command->info("- {$status}: {$count} ({$percentage}%)");
        }
    }
}