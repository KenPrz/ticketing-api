<?php

namespace Database\Seeders;

use App\Enums\EventImageType;
use App\Enums\MerchandiseStatus;
use App\Enums\UserTypes;
use App\Models\{
    Event,
    EventImage as Image,
    Merchandise,
    User,
};
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\File;
use Faker\Generator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{

    /**
     * The base image link for event images (fallback).
     *
     * @var string
     */
    private const IMAGE_BASE_LINK = 'https://placehold.co/600x400/';

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
        $events = collect(config('events'));

        $events->each(function ($event) {
            $event = Event::create([
                'name' => $event['name'],
                'category' => $event['category'],
                'organizer_id' => User::where('user_type', UserTypes::ORGANIZER->value)
                    ->inRandomOrder()
                    ->first()->id,
                'date' => $event['date'],
                'time' => $event['time'],
                'description' => $event['description'],
                'venue' => $event['venue'],
                'city' => $event['address'],
                'longitude' => $event['longitude'],
                'latitude' => $event['latitude'],
                'is_published' => true,
                'is_cancelled' => false,
                'cancelled_reason' => null,
                'cancelled_at' => null,
                'published_at' => now(),
            ]);

            $color = strtolower($this->faker->colorName());

            // Create a Banner
            Image::create([
                'imageable_id' => $event->id,
                'imageable_type' => Event::class,
                'image_url' => $this->imageLinkGenerator(
                    $event,
                    EventImageType::BANNER,
                    $color,
                ),
                'image_type' => EventImageType::BANNER->value,
            ]);

            // Create a Thumbnail
            Image::create([
                'imageable_id' => $event->id,
                'imageable_type' => Event::class,
                'image_url' => $this->imageLinkGenerator(
                    $event,
                    EventImageType::THUMBNAIL,
                    $color,
                ),
                'image_type' => EventImageType::THUMBNAIL->value,
            ]);

            // Create a venue image
            Image::create([
                'imageable_id' => $event->id,
                'imageable_type' => Event::class,
                'image_url' => $this->imageLinkGenerator(
                    $event,
                    EventImageType::VENUE,
                    $color,
                ),
                'image_type' => EventImageType::VENUE->value,
            ]);

            // Create a seat plan image
            Image::create([
                'imageable_id' => $event->id,
                'imageable_type' => Event::class,
                'image_url' => $this->moveSeatPlanToStorage(
                    EventImageType::SEAT_PLAN,
                ),
                'image_type' => EventImageType::SEAT_PLAN->value,
            ]);

            // Determine the number of images to create for the gallery
            $numOfImages = rand(2, 5);

            // Create a Gallery
            for ($i = 0; $i < $numOfImages; $i++) {
                Image::create([
                    'imageable_id' => $event->id,
                    'imageable_type' => Event::class,
                    'image_url' => $this->imageLinkGenerator(
                        $event,
                        EventImageType::GALLERY,
                        $color,
                    ),
                    'image_type' => EventImageType::GALLERY->value,
                ]);
            }

            // Create merchandise items with images
            $numOfMerchandise = rand(1, 6);

            for ($i = 0; $i < $numOfMerchandise; $i++) {
                // Create a merchandise item for this event
                $merchandise = Merchandise::create([
                    'event_id' => $event->id,
                    'name' => $this->faker->words(rand(2, 4), true),
                    'description' => $this->faker->sentence(rand(5, 10)),
                    'price' => $this->faker->randomFloat(2, 10, 100),
                    'stock' => $this->faker->numberBetween(10, 1000),
                    'status' => $this->faker->randomElement(MerchandiseStatus::cases()),
                ]);
                
                // Create the merchandise image using polymorphic relationship
                Image::create([
                    'imageable_id' => $merchandise->id,
                    'imageable_type' => Merchandise::class,
                    'image_url' => $this->imageLinkGenerator(
                        $merchandise,
                        EventImageType::MERCHANDISE,
                        $color,
                    ),
                    'image_type' => EventImageType::MERCHANDISE->value,
                ]);
            }
        });

        $organizerIds = Event::all()->pluck('organizer_id');

        $organizersWithoutEvents = User::where('user_type', UserTypes::ORGANIZER->value)
            ->whereNotIn('id', $organizerIds)
            ->get();

        $organizersWithoutEvents->each(function ($user) {
            Event::create([
                'name' => $this->faker->sentence(rand(2, 4)),
                'organizer_id' => $user->id,
                'date' => $this->faker->dateTimeBetween('now', '+1 year'),
                'time' => $this->faker->time(),
                'description' => $this->faker->sentence(rand(10, 20)),
                'venue' => $this->faker->sentence(rand(2, 4)),
                'city' => $this->faker->city(),
            ]);
        });
    }

    /**
     * Generate an image path for the event or merchandise.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param \App\Enums\EventImageType $type
     * @param string $color The color to use for the image (not used anymore)
     *
     * @return string
     */
    private function imageLinkGenerator(
        $model,
        EventImageType $type,
        string $color,
    ) {
        // Get sample images from public directory
        $sampleImages = glob(public_path('sample_images/*.png'));
        
        // Check if images exist
        if (empty($sampleImages)) {
            // Fallback to placeholder if no sample images found
            return self::IMAGE_BASE_LINK . $color . '/white?text=' . urlencode($type->value) . '+' . urlencode($model->name);
        }

        // Select a random image
        $randomImage = $sampleImages[array_rand($sampleImages)];
        
        // Determine storage path based on image type
        $modelType = ($model instanceof Event) ? 'events' : 'merchandise';
        $storagePath = "{$modelType}/{$model->id}/" . strtolower($type->value);
        
        // Create a unique filename
        $filename = Str::uuid() . '.png';
        
        // Copy the image to the storage path
        $imagePath = Storage::disk('public')->putFileAs(
            $storagePath, 
            new File($randomImage), 
            $filename
        );
        
        // Return just the path without the full URL
        return $imagePath;
    }

    /**
     * Move seat plan image to storage and return the path
     *
     * @param \App\Enums\EventImageType $type
     *
     * @return string
     */
    private function moveSeatPlanToStorage(EventImageType $type)
    {
        // Get the specific seat plan file
        $seatPlan = public_path('sample_images/seat_plans/seat_pan_1.jpg');

        // Check if file exists
        if (!file_exists($seatPlan)) {
            // Return a placeholder or handle the error
            return self::IMAGE_BASE_LINK . 'gray/white?text=seat_plan_not_found';
        }
        
        // Determine storage path
        $storagePath = 'events/seat_plans';
        
        // Create a unique filename
        $filename = Str::uuid() . '.jpg';
        
        // Copy the image to the storage path
        $imagePath = Storage::disk('public')->putFileAs(
            $storagePath,
            new File($seatPlan),
            $filename
        );
        
        // Return just the path without the full URL
        return $imagePath;
    }
}