<?php

namespace Database\Seeders;

use App\Enums\ReadStatus;
use App\Enums\UserTypes;
use Illuminate\Database\Seeder;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Faker\Generator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class ChatSeeder extends Seeder
{
    /**
     * The Faker instance.
     *
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * Number of chats to create per user
     */
    public const CHATS_PER_USER = 3;

    /**
     * Max number of messages per chat
     */
    public const MAX_MESSAGES_PER_CHAT = 10;

    /**
     * Construct the faker instance.
     *
     * @param \Faker\Generator $faker
     *
     * @return void
     */
    public function __construct(Generator $faker)
    {
        $this->faker = $faker;
    }

    public function run()
    {
        $output = new ConsoleOutput();
        $output->writeln('Starting to seed chats and messages...');

        // Get all users with exposed user types
        $users = User::whereIn('user_type', UserTypes::exposedUserTypes())->get();
        $userCount = $users->count();
        
        if ($userCount < 2) {
            $output->writeln('Not enough users to create chats. Need at least 2 users.');
            return;
        }

        $output->writeln("Found {$userCount} users to create chats for");
        $progressBar = new ProgressBar($output, $userCount);
        $progressBar->start();

        // Create a mapping of existing chats to avoid duplicates
        $existingChatPairs = $this->getExistingChatPairs();
        
        $batchSize = 100;
        $chatInserts = [];
        $messageInserts = [];
        
        foreach ($users as $user) {
            // For each user, create multiple chats with different random users
            $chatCount = 0;
            $potentialPartners = $users->where('id', '!=', $user->id)->shuffle();
            
            foreach ($potentialPartners as $partner) {
                if ($chatCount >= self::CHATS_PER_USER) {
                    break;
                }
                
                // Sort the user IDs to ensure consistency
                $userIds = [$user->id, $partner->id];
                sort($userIds);
                
                // Check if this chat already exists
                $chatKey = $userIds[0] . '-' . $userIds[1];
                if (isset($existingChatPairs[$chatKey])) {
                    continue;
                }
                
                // Mark this chat as processed
                $existingChatPairs[$chatKey] = true;
                
                // Create a new chat
                $createdAt = $this->faker->dateTimeBetween('-6 months', 'now');
                $chatId = DB::table('chats')->insertGetId([
                    'user_one_id' => $userIds[0],
                    'user_two_id' => $userIds[1],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
                
                // Create messages for this chat
                $messageCount = $this->faker->numberBetween(1, self::MAX_MESSAGES_PER_CHAT);
                $chatUsers = [$user->id, $partner->id];
                
                for ($i = 0; $i < $messageCount; $i++) {
                    // Alternate between users for a conversation feel
                    $senderId = $chatUsers[$i % 2];
                    $isRead = $this->faker->boolean(80); // 80% chance message is read
                    
                    $messageCreatedAt = $this->faker->dateTimeBetween($createdAt, 'now');
                    
                    $messageInserts[] = [
                        'chat_id' => $chatId,
                        'user_id' => $senderId,
                        'content' => $this->faker->realText(100),
                        'read_status' => $isRead ? ReadStatus::READ->value : ReadStatus::UNREAD->value,
                        'created_at' => $messageCreatedAt,
                        'updated_at' => $messageCreatedAt,
                    ];
                    
                    // Batch insert messages when we reach the batch size
                    if (count($messageInserts) >= $batchSize) {
                        DB::table('messages')->insert($messageInserts);
                        $messageInserts = [];
                    }
                }
                
                $chatCount++;
            }
            
            $progressBar->advance();
        }
        
        // Insert any remaining messages
        if (!empty($messageInserts)) {
            DB::table('messages')->insert($messageInserts);
        }

        $progressBar->finish();
        $output->writeln('');
        $output->writeln('Chat seeding complete!');
    }

    /**
     * Get existing chat pairs to avoid duplicates
     * 
     * @return array
     */
    protected function getExistingChatPairs()
    {
        $pairs = [];
        $existingChats = DB::table('chats')->select('user_one_id', 'user_two_id')->get();
        
        foreach ($existingChats as $chat) {
            $key = $chat->user_one_id . '-' . $chat->user_two_id;
            $pairs[$key] = true;
        }
        
        return $pairs;
    }
}