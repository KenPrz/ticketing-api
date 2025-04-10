<?php

namespace Database\Factories;

use App\Enums\UserTypes;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected const AVATARS = [
        '/storage/images/avatars/img_1.png',
        '/storage/images/avatars/img_2.png',
        '/storage/images/avatars/img_3.png',
        '/storage/images/avatars/img_4.png',
        '/storage/images/avatars/img_5.png',
        '/storage/images/avatars/img_6.png',
        '/storage/images/avatars/img_7.png',
        '/storage/images/avatars/img_8.png',
        '/storage/images/avatars/img_9.png',
        '/storage/images/avatars/img_10.png',
    ];

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker
                ->unique()
                ->safeEmail(),
            'avatar' => rtrim(env('APP_URL'), '/') . $this->faker->randomElement(self::AVATARS),
            'mobile' => $this->faker
                ->unique()
                ->numerify('+639#########'),
            'user_type' => $this->faker->randomElement(UserTypes::list())->value,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
            'recent_longitude' => $this->generateNcrCoordinates()['longitude'],
            'recent_latitude' => $this->generateNcrCoordinates()['latitude'],
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
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
