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
        
        // Make sure friendship table is empty before starting
        // IMPORTANT: Do this OUTSIDE the transaction because truncate implicitly commits
        $this->command->info("Truncating existing friendship records...");
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        UserFriend::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Process users in chunks to avoid memory issues
        User::query()->chunkById(500, function ($users) use ($bar) {
            $friendships = [];
            $now = Carbon::now();
            $processedPairs = [];
            
            foreach ($users as $user) {
                // Get random users that aren't the current user
                // Client users should have more friendship relationships
                $limit = $user->user_type === UserTypes::CLIENT->value ? 8 : 5;
                
                $potentialFriends = User::where('id', '!=', $user->id)
                    ->inRandomOrder()
                    ->limit($limit)
                    ->get(['id', 'user_type']);
                
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
                    // 60% ACCEPTED, 25% PENDING, 10% REJECTED, 5% BLOCKED
                    $rand = mt_rand(1, 100);
                    
                    if ($rand <= 60) {
                        // ACCEPTED - In the updated friendship model, we create one record
                        // with the initiator as user_id and recipient as friend_id
                        
                        // Determine who initiated the friendship randomly
                        if (mt_rand(0, 1) === 0) {
                            // User initiated
                            $friendships[] = [
                                'user_id' => $user->id,
                                'friend_id' => $friend->id,
                                'status' => FriendStatus::ACCEPTED->value,
                                'created_at' => $now->copy()->subDays(rand(1, 30)),
                                'updated_at' => $now,
                            ];
                        } else {
                            // Friend initiated
                            $friendships[] = [
                                'user_id' => $friend->id,
                                'friend_id' => $user->id,
                                'status' => FriendStatus::ACCEPTED->value,
                                'created_at' => $now->copy()->subDays(rand(1, 30)),
                                'updated_at' => $now,
                            ];
                        }
                        
                    } elseif ($rand <= 85) {
                        // PENDING - A sent request to B, but B hasn't responded
                        // Determine who sends the request randomly
                        if (mt_rand(0, 1) === 0) {
                            // User sent request to friend
                            $friendships[] = [
                                'user_id' => $user->id,
                                'friend_id' => $friend->id,
                                'status' => FriendStatus::PENDING->value,
                                'created_at' => $now->copy()->subDays(rand(1, 7)),
                                'updated_at' => $now->copy()->subDays(rand(1, 7)),
                            ];
                        } else {
                            // Friend sent request to user
                            $friendships[] = [
                                'user_id' => $friend->id,
                                'friend_id' => $user->id,
                                'status' => FriendStatus::PENDING->value,
                                'created_at' => $now->copy()->subDays(rand(1, 7)),
                                'updated_at' => $now->copy()->subDays(rand(1, 7)),
                            ];
                        }
                        
                    } elseif ($rand <= 95) {
                        // REJECTED - A sent request to B, but B rejected it
                        // For simplicity in our model, this is stored as a record with REJECTED status
                        if (mt_rand(0, 1) === 0) {
                            // User sent request, friend rejected
                            $friendships[] = [
                                'user_id' => $user->id,
                                'friend_id' => $friend->id,
                                'status' => FriendStatus::REJECTED->value,
                                'created_at' => $now->copy()->subDays(rand(8, 14)),
                                'updated_at' => $now->copy()->subDays(rand(1, 7)),
                            ];
                        } else {
                            // Friend sent request, user rejected
                            $friendships[] = [
                                'user_id' => $friend->id,
                                'friend_id' => $user->id,
                                'status' => FriendStatus::REJECTED->value,
                                'created_at' => $now->copy()->subDays(rand(8, 14)),
                                'updated_at' => $now->copy()->subDays(rand(1, 7)),
                            ];
                        }
                        
                    } else {
                        // BLOCKED - A knows B but has blocked them
                        if (mt_rand(0, 1) === 0) {
                            // User blocked friend
                            $friendships[] = [
                                'user_id' => $user->id,
                                'friend_id' => $friend->id,
                                'status' => FriendStatus::BLOCKED->value,
                                'created_at' => $now->copy()->subDays(rand(1, 14)),
                                'updated_at' => $now,
                            ];
                        } else {
                            // Friend blocked user
                            $friendships[] = [
                                'user_id' => $friend->id,
                                'friend_id' => $user->id,
                                'status' => FriendStatus::BLOCKED->value,
                                'created_at' => $now->copy()->subDays(rand(1, 14)),
                                'updated_at' => $now,
                            ];
                        }
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
        
        // Finish the progress bar
        $bar->finish();
        
        // Add a new line after the progress bar
        $this->command->newLine();
        $this->command->info('Successfully seeded ' . UserFriend::count() . ' friend relationships.');
        
        // Show distribution of statuses
        $this->showStatusDistribution();
        
        // Create specific friendships for testing
        $this->createTestFriendships();
    }
    
    /**
     * Create specific friendship scenarios for testing
     */
    private function createTestFriendships(): void
    {
        $this->command->info('Creating test friendship scenarios...');
        
        // Find some specific users to use for testing
        // We'll use user IDs 2-6 for these test scenarios
        $testUsers = User::whereIn('id', [2, 3, 4, 5, 6])->get();
        
        if ($testUsers->count() < 5) {
            $this->command->warn('Not enough users found for creating test scenarios. Skipping.');
            return;
        }
        
        // Make sure any existing relationships between these users are removed
        UserFriend::whereIn('user_id', [2, 3, 4, 5, 6])
            ->whereIn('friend_id', [2, 3, 4, 5, 6])
            ->delete();
        
        $now = Carbon::now();
        
        // Scenario 1: User 2 and User 3 are friends
        UserFriend::create([
            'user_id' => 2,
            'friend_id' => 3,
            'status' => FriendStatus::ACCEPTED->value,
            'created_at' => $now->copy()->subDays(10),
            'updated_at' => $now->copy()->subDays(9),
        ]);
        
        // Scenario 2: User 4 sent a request to User 2
        UserFriend::create([
            'user_id' => 4,
            'friend_id' => 2,
            'status' => FriendStatus::PENDING->value,
            'created_at' => $now->copy()->subDays(3),
            'updated_at' => $now->copy()->subDays(3),
        ]);
        
        // Scenario 3: User 2 sent a request to User 5
        UserFriend::create([
            'user_id' => 2,
            'friend_id' => 5,
            'status' => FriendStatus::PENDING->value,
            'created_at' => $now->copy()->subDays(2),
            'updated_at' => $now->copy()->subDays(2),
        ]);
        
        // Scenario 4: User 2 blocked User 6
        UserFriend::create([
            'user_id' => 2,
            'friend_id' => 6,
            'status' => FriendStatus::BLOCKED->value,
            'created_at' => $now->copy()->subDays(5),
            'updated_at' => $now->copy()->subDays(5),
        ]);
        
        $this->command->info('Created test friendship scenarios successfully!');
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