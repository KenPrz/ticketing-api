<?php

namespace Database\Seeders;

use App\Enums\UserTypes;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\Console\Helper\ProgressBar;

class UserSeeder extends Seeder
{
    /**
     * Prevent Eloquent from firing model events.
     */
    use WithoutModelEvents;

    /**
     * The Faker instance.
     */
    protected $faker;

    /**
     * Construct the faker instance.
     */
    public function __construct()
    {
        $this->faker = \Faker\Factory::create();
    }

    /**
     * The user counts.
     * 
     * @var array<int>
     */
    protected const USER_COUNTS = [
        'client' => 8000,
        'admin' => 5,
        'organizer' => 10,
    ];

    /**
     * The batch size for insertions.
     * 
     * @var int
     */
    protected const BATCH_SIZE = 500;

    /**
     * The default password.
     * 
     * @var string
     */
    protected const DEFAULT_PASSWORD = 'password';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $output = $this->command->getOutput();
        $startTime = microtime(true);
        
        // Process each user type
        foreach (UserTypes::cases() as $userType) {
            $type = strtolower($userType->name);
            
            // Skip if not in our count array
            if (!isset(self::USER_COUNTS[$type])) {
                continue;
            }
            
            $count = self::USER_COUNTS[$type];
            if ($count <= 0) {
                continue;
            }
            
            $this->command->info("Seeding {$type}s ({$count})...");
            
            // Create progress bar
            $progressBar = new ProgressBar($output, $count);
            $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
            $progressBar->start();
            
            // Generate and insert records in batches
            $remaining = $count;
            $hashedPassword = Hash::make(self::DEFAULT_PASSWORD);
            
            while ($remaining > 0) {
                $batchSize = min(self::BATCH_SIZE, $remaining);
                $users = [];
                
                for ($i = 0; $i < $batchSize; $i++) {
                    $users[] = [
                        'name' => $this->faker->name(),
                        'email' => $this->faker->unique()->safeEmail(),
                        'mobile' => $this->faker->unique()->numerify('+639#########'),
                        'user_type' => $userType->value,
                        'email_verified_at' => now(),
                        'password' => $hashedPassword,
                        'remember_token' => Str::random(10),
                        'created_at' => now(),
                        'updated_at' => now(),
                        'recent_longitude' => $this->generateNcrCoordinates()['longitude'],
                        'recent_latitude' => $this->generateNcrCoordinates()['latitude'],
                    ];
                }
                
                // Insert batch
                DB::table('users')->insert($users);
                
                // Update progress
                $progressBar->advance($batchSize);
                $remaining -= $batchSize;
            }
            
            $progressBar->finish();
            $output->writeln('');
        }
        
        // Calculate and show execution time
        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);
        
        // Output a success message once the users have been seeded
        $total = array_sum(self::USER_COUNTS);
        $this->command->info("Users seeded successfully in {$executionTime} seconds!");
        $this->command->info("Created {$total} users: {$this->getUserCountSummary()}");
    }
    
    /**
     * Get a summary string of user counts by type.
     *
     * @return string
     */
    protected function getUserCountSummary(): string
    {
        $parts = [];
        foreach (self::USER_COUNTS as $type => $count) {
            $parts[] = "{$count} {$type}s";
        }
        
        return implode(', ', $parts);
    }
    
    /**
     * Generate random coordinates within NCR municipalities
     * 
     * @return array
     */
    protected function generateNcrCoordinates(): array
    {
        $ncrAreas = [
            'Manila' => ['lat' => 14.5995, 'lng' => 120.9842],
            'Quezon City' => ['lat' => 14.6760, 'lng' => 121.0437],
            'Makati' => ['lat' => 14.5547, 'lng' => 121.0244],
            'Taguig' => ['lat' => 14.5176, 'lng' => 121.0509],
            'Pasig' => ['lat' => 14.5764, 'lng' => 121.0851],
            'Parañaque' => ['lat' => 14.4793, 'lng' => 121.0198],
            'Pasay' => ['lat' => 14.5378, 'lng' => 121.0014],
            'Caloocan' => ['lat' => 14.6507, 'lng' => 120.9830],
            'Muntinlupa' => ['lat' => 14.4193, 'lng' => 121.0413],
            'Marikina' => ['lat' => 14.6507, 'lng' => 121.1029],
            'Valenzuela' => ['lat' => 14.7011, 'lng' => 120.9830],
            'Las Piñas' => ['lat' => 14.4473, 'lng' => 120.9837],
            'Mandaluyong' => ['lat' => 14.5794, 'lng' => 121.0359],
            'San Juan' => ['lat' => 14.6019, 'lng' => 121.0355],
            'Navotas' => ['lat' => 14.6667, 'lng' => 120.9427],
            'Malabon' => ['lat' => 14.6681, 'lng' => 120.9625],
            'Pateros' => ['lat' => 14.5456, 'lng' => 121.0685],
        ];
        
        // Select a random NCR area
        $area = array_rand($ncrAreas);
        $coordinates = $ncrAreas[$area];
        
        // Add small random variation (±0.01 degrees ≈ 1km)
        return [
            'latitude' => $coordinates['lat'] + $this->faker->randomFloat(6, -0.01, 0.01),
            'longitude' => $coordinates['lng'] + $this->faker->randomFloat(6, -0.01, 0.01),
        ];
    }
}
