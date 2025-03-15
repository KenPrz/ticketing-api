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
     * The events to seed.
     *
     * @var array
     */
    private const EVENTS = [
        [
            'name' => 'Niall Horan: "The Show" Live',
            'date' => '2024-05-13', // MAY 13
            'time' => '7:30PM - 10:00PM',
            'venue' => 'Mall Of Asia Arena',
            'address' => 'Pasay',
            'description' => 'Catch Niall Horan live as he performs his greatest hits from his latest album "The Show" and more! Experience an unforgettable night of music with one of pop\'s biggest stars. The former One Direction member brings his solo tour to Manila for the first time, showcasing his musical evolution and chart-topping songs.',
        ],
        [
            'name' => 'BINIverse',
            'date' => '2024-01-28', // JAN 28
            'time' => '6:00PM - 9:00PM',
            'venue' => 'New Frontier Theater',
            'address' => 'Cubao',
            'description' => 'Join BINI, the Nation\'s Girl Group, for an exciting concert experience! BINI Universe showcases the group\'s talent, charisma, and hit songs in an immersive show designed for their fans. Get ready for stunning performances, beautiful vocals, and powerful choreography that will leave you breathless.',
        ],
        [
            'name' => 'Taylor Swift: The Eras Tour',
            'date' => '2024-02-03', // FEB 03
            'time' => '6:00PM - 11:00PM',
            'venue' => 'Philippine Arena',
            'address' => 'Bulacan',
            'description' => 'The most anticipated concert event of the year! Taylor Swift brings her record-breaking Eras Tour to the Philippines for the first time. Join thousands of Swifties for this once-in-a-lifetime concert experience spanning all eras of Taylor\'s illustrious career. Featuring hit songs from all her albums, stunning visuals, and unforgettable performances.',
        ],
        [
            'name' => 'Coldplay: Music of the Spheres',
            'date' => '2024-02-19', // FEB 19
            'time' => '7:00PM - 10:30PM',
            'venue' => 'Philippine Arena',
            'address' => 'Bulacan',
            'description' => 'Coldplay returns to the Philippines with their spectacular Music of the Spheres World Tour! Experience the band\'s acclaimed live show featuring stunning visuals, interactive light displays, and all their greatest hits. The eco-friendly concert showcases Coldplay\'s commitment to sustainability while delivering an unforgettable musical journey.',
        ],
        [
            'name' => 'Eraserheads Reunion',
            'date' => '2024-02-25', // FEB 25
            'time' => '7:00PM - 11:00PM',
            'venue' => 'Rizal Park',
            'address' => 'Manila',
            'description' => 'The legendary Eraserheads reunite for a special concert under the stars at Rizal Park. Witness Philippine rock history as Ely, Marcus, Buddy, and Raymund perform their iconic hits that defined a generation. From "Ang Huling El Bimbo" to "Alapaap," relive the magic of the band\'s extensive catalog in this must-see event.',
        ],
        [
            'name' => 'Ben&Ben Live',
            'date' => '2024-04-05', // APR 05
            'time' => '6:30PM - 9:30PM',
            'venue' => 'BGC Amphitheater',
            'address' => 'Taguig',
            'description' => 'Experience the heartfelt music of Ben&Ben in an intimate outdoor setting. The 9-piece folk-pop band will perform their hit songs along with new material from their latest album. Known for their poetic lyrics and unique instrumentation, this concert promises an evening of musical connection and emotional depth.',
        ],
        [
            'name' => 'FIBA Asia 2025 Qualifiers',
            'date' => '2024-02-22', // FEB 22
            'time' => '7:00PM - 9:00PM',
            'venue' => 'Mall Of Asia Arena',
            'address' => 'Pasay',
            'description' => 'Cheer on the Philippines as they face tough competition in the FIBA Asia 2025 Qualifiers. Witness international basketball at its finest as our national team competes for a spot in the prestigious FIBA Asia Cup. The qualifying match features our country\'s best players showing their skills and national pride.',
        ],
        [
            'name' => 'PVL 2024',
            'date' => '2024-03-03', // MAR 03
            'time' => '2:00PM - 6:00PM',
            'venue' => 'Smart Araneta',
            'address' => 'Cubao',
            'description' => 'Watch the Premier Volleyball League (PVL) as the top volleyball teams in the Philippines compete for championship glory. This match features exciting rallies, powerful spikes, and amazing defensive plays. Support your favorite teams and players in this thrilling display of volleyball excellence.',
        ],
        [
            'name' => 'PBA All Star Game',
            'date' => '2024-03-12', // MAR 12
            'time' => '5:00PM - 8:00PM',
            'venue' => 'Smart Araneta',
            'address' => 'Cubao',
            'description' => 'The PBA All-Star Game brings together the brightest stars of Philippine Basketball for an exhibition of skill, entertainment, and friendly competition. Featuring skills challenges, a three-point shootout, slam dunk contest, and the main All-Star Game, this is basketball entertainment at its finest for fans of all ages.',
        ],
        [
            'name' => 'UAAP Volleyball Finals',
            'date' => '2024-04-18', // APR 18
            'time' => '3:00PM - 7:00PM',
            'venue' => 'Mall Of Asia Arena',
            'address' => 'Pasay',
            'description' => 'Experience the intensity of the UAAP Volleyball Finals as the top university teams battle for the championship. The culmination of months of competition, this match promises thrilling exchanges, school spirit, and elite collegiate volleyball action. Support your alma mater or favorite university in this exciting sports event.',
        ],
        [
            'name' => 'Disney on Ice',
            'date' => '2024-03-25', // MAR 25
            'time' => '1:00PM - 3:00PM, 6:00PM - 8:00PM',
            'venue' => 'Smart Araneta',
            'address' => 'Cubao',
            'description' => 'Disney On Ice presents magical ice skating performances featuring beloved Disney characters. Watch as skilled skaters bring your favorite Disney stories to life through stunning choreography, colorful costumes, and memorable music. An enchanting experience for Disney fans of all ages that creates lasting family memories.',
        ],
        [
            'name' => 'Kidzoona Play Festival',
            'date' => '2024-02-15', // FEB 15
            'time' => '10:00AM - 8:00PM',
            'venue' => 'SMX Convention',
            'address' => 'Pasay',
            'description' => 'The Kidzoona Play Festival transforms SMX Convention Center into the ultimate children\'s playground. With inflatable attractions, ball pits, interactive games, and creative zones, kids can play, explore, and have fun in a safe environment. Special appearances by children\'s characters and entertaining shows throughout the day make this a complete family event.',
        ],
        [
            'name' => 'Philippine Philharmonic Orchestra',
            'date' => '2024-02-28', // FEB 28
            'time' => '8:00PM - 10:00PM',
            'venue' => 'Cultural Center',
            'address' => 'Manila',
            'description' => 'Experience the musical excellence of the Philippine Philharmonic Orchestra in this special concert featuring both classical masterpieces and Filipino compositions. Under the baton of their acclaimed conductor, the orchestra delivers a program that showcases technical brilliance and emotional depth. An evening of orchestral beauty in the iconic Cultural Center of the Philippines.',
        ],
        [
            'name' => 'Art in the Park 2025',
            'date' => '2024-04-10', // APR 10
            'time' => '10:00AM - 10:00PM',
            'venue' => 'Jaime Velasquez Park',
            'address' => 'Makati',
            'description' => 'Art in the Park transforms Jaime Velasquez Park into an outdoor art gallery featuring affordable artworks from leading and emerging Filipino artists. Browse paintings, sculptures, prints, and mixed media pieces in a relaxed park setting. With live music, food stalls, and interactive art activities, this annual event makes art accessible to everyone while supporting the local art community.',
        ],
        [
            'name' => 'Manila Food Festival',
            'date' => '2024-03-25', // MAR 25
            'time' => '11:00AM - 10:00PM',
            'venue' => 'Bonifacio High Street',
            'address' => 'BGC',
            'description' => 'Savor the diverse flavors of the Philippines at the Manila Food Festival. This culinary celebration features food stalls from the country\'s best restaurants, street food vendors, and innovative chefs. Sample regional specialties, trendy fusion dishes, and classic Filipino favorites while enjoying cooking demonstrations, food competitions, and live entertainment throughout the day.',
        ],
        [
            'name' => 'Clark Aurora Music Festival',
            'date' => '2024-02-10', // FEB 10
            'time' => '3:00PM - 1:00AM',
            'venue' => 'Clark Global City Park',
            'address' => 'Pampanga',
            'description' => 'The Clark Aurora Music Festival brings together top artists from multiple genres for a day-long celebration of music under the stars. With multiple stages, food concessions, art installations, and camping options, this festival offers a complete experience for music lovers. The lineup features both international acts and homegrown Filipino talent in a picturesque outdoor setting.',
        ],
        [
            'name' => 'Street Food Night Market',
            'date' => '2024-03-20', // MAR 20
            'time' => '5:00PM - 12:00AM',
            'venue' => 'Circuit Makati',
            'address' => 'Makati',
            'description' => 'Experience the vibrant street food culture of the Philippines at this lively night market. Dozens of vendors offer everything from classic Filipino street fare to international snacks with local twists. The market features atmospheric lighting, live acoustic performances, and communal dining areas, creating a festive social environment perfect for foodies and night owls.',
        ],
        [
            'name' => 'TWICE x ONCE',
            'date' => '2024-03-15', // MAR 15
            'time' => '6:30PM - 9:30PM',
            'venue' => 'Mall Of Asia',
            'address' => 'Pasay',
            'description' => 'TWICE brings their electrifying performance to Manila! The globally acclaimed K-pop girl group will showcase their impressive repertoire of hits in this highly anticipated concert. ONCE (TWICE\'s official fandom) can look forward to spectacular choreography, stunning visuals, and the group\'s signature infectious energy in an unforgettable show that celebrates the special bond between TWICE and their fans.',
        ],
    ];

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
        $events = collect(self::EVENTS);
        $events->each(function ($event) {
            Event::create([
                'name' => $event['name'],
                'organizer_id' => User::where('user_type', UserTypes::ORGANIZER->value)
                    ->inRandomOrder()
                    ->first()->id,
                'date' => $event['date'],
                'time' => $event['time'],
                'description' => $event['description'],
                'venue' => $event['venue'],
                'city' => $event['address'],
            ]);
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
}
