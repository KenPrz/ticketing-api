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
     * The base image link for event images.
     *
     * @var string
     */
    private const IMAGE_BASE_LINK = 'https://placehold.co/600x400/';

/**
     * The events to seed.
     *
     * @var array
     */
    private const EVENTS = [
        [
            'name' => 'Niall Horan: "The Show" Live',
            'date' => '2025-05-13',
            'time' => '7:30PM - 10:00PM',
            'venue' => 'Mall Of Asia Arena',
            'address' => 'Pasay',
            'description' => 'Catch Niall Horan live as he performs his greatest hits from his latest album "The Show" and more! Experience an unforgettable night of music with one of pop\'s biggest stars. The former One Direction member brings his solo tour to Manila for the first time, showcasing his musical evolution and chart-topping songs.',
            'longitude' => 120.9841,
            'latitude' => 14.5344,
            'category' => 'CONCERT'
        ],
        [
            'name' => 'BINIverse',
            'date' => '2025-03-28',
            'time' => '6:00PM - 9:00PM',
            'venue' => 'New Frontier Theater',
            'address' => 'Cubao',
            'description' => 'Join BINI, the Nation\'s Girl Group, for an exciting concert experience! BINI Universe showcases the group\'s talent, charisma, and hit songs in an immersive show designed for their fans. Get ready for stunning performances, beautiful vocals, and powerful choreography that will leave you breathless.',
            'longitude' => 121.0529,
            'latitude' => 14.6217,
            'category' => 'CONCERT'
        ],
        [
            'name' => 'Taylor Swift: The Eras Tour',
            'date' => '2025-04-03',
            'time' => '6:00PM - 11:00PM',
            'venue' => 'Philippine Arena',
            'address' => 'Bulacan',
            'description' => 'The most anticipated concert event of the year! Taylor Swift brings her record-breaking Eras Tour to the Philippines for the first time. Join thousands of Swifties for this once-in-a-lifetime concert experience spanning all eras of Taylor\'s illustrious career. Featuring hit songs from all her albums, stunning visuals, and unforgettable performances.',
            'longitude' => 120.9481,
            'latitude' => 14.7631,
            'category' => 'CONCERT'
        ],
        [
            'name' => 'Coldplay: Music of the Spheres',
            'date' => '2025-06-19',
            'time' => '7:00PM - 10:30PM',
            'venue' => 'Philippine Arena',
            'address' => 'Bulacan',
            'description' => 'Coldplay returns to the Philippines with their spectacular Music of the Spheres World Tour! Experience the band\'s acclaimed live show featuring stunning visuals, interactive light displays, and all their greatest hits. The eco-friendly concert showcases Coldplay\'s commitment to sustainability while delivering an unforgettable musical journey.',
            'longitude' => 120.9481,
            'latitude' => 14.7631,
            'category' => 'CONCERT'
        ],
        [
            'name' => 'Eraserheads Reunion',
            'date' => '2025-04-25',
            'time' => '7:00PM - 11:00PM',
            'venue' => 'Rizal Park',
            'address' => 'Manila',
            'description' => 'The legendary Eraserheads reunite for a special concert under the stars at Rizal Park. Witness Philippine rock history as Ely, Marcus, Buddy, and Raymund perform their iconic hits that defined a generation. From "Ang Huling El Bimbo" to "Alapaap," relive the magic of the band\'s extensive catalog in this must-see event.',
            'longitude' => 120.9794,
            'latitude' => 14.5831,
            'category' => 'CONCERT'
        ],
        [
            'name' => 'Ben&Ben Live',
            'date' => '2025-04-05',
            'time' => '6:30PM - 9:30PM',
            'venue' => 'BGC Amphitheater',
            'address' => 'Taguig',
            'description' => 'Experience the heartfelt music of Ben&Ben in an intimate outdoor setting. The 9-piece folk-pop band will perform their hit songs along with new material from their latest album. Known for their poetic lyrics and unique instrumentation, this concert promises an evening of musical connection and emotional depth.',
            'longitude' => 121.0500,
            'latitude' => 14.5478,
            'category' => 'CONCERT'
        ],
        [
            'name' => 'FIBA Asia 2025 Qualifiers',
            'date' => '2025-03-22',
            'time' => '7:00PM - 9:00PM',
            'venue' => 'Mall Of Asia Arena',
            'address' => 'Pasay',
            'description' => 'Cheer on the Philippines as they face tough competition in the FIBA Asia 2025 Qualifiers. Witness international basketball at its finest as our national team competes for a spot in the prestigious FIBA Asia Cup. The qualifying match features our country\'s best players showing their skills and national pride.',
            'longitude' => 120.9841,
            'latitude' => 14.5344,
            'category' => 'SPORTS'
        ],
        [
            'name' => 'PVL 2025',
            'date' => '2025-05-03',
            'time' => '2:00PM - 6:00PM',
            'venue' => 'Smart Araneta',
            'address' => 'Cubao',
            'description' => 'Watch the Premier Volleyball League (PVL) as the top volleyball teams in the Philippines compete for championship glory. This match features exciting rallies, powerful spikes, and amazing defensive plays. Support your favorite teams and players in this thrilling display of volleyball excellence.',
            'longitude' => 121.0514,
            'latitude' => 14.6197,
            'category' => 'SPORTS'
        ],
        [
            'name' => 'PBA All Star Game',
            'date' => '2025-04-12',
            'time' => '5:00PM - 8:00PM',
            'venue' => 'Smart Araneta',
            'address' => 'Cubao',
            'description' => 'The PBA All-Star Game brings together the brightest stars of Philippine Basketball for an exhibition of skill, entertainment, and friendly competition. Featuring skills challenges, a three-point shootout, slam dunk contest, and the main All-Star Game, this is basketball entertainment at its finest for fans of all ages.',
            'longitude' => 121.0514,
            'latitude' => 14.6197,
            'category' => 'SPORTS'
        ],
        [
            'name' => 'UAAP Volleyball Finals',
            'date' => '2025-05-18',
            'time' => '3:00PM - 7:00PM',
            'venue' => 'Mall Of Asia Arena',
            'address' => 'Pasay',
            'description' => 'Experience the intensity of the UAAP Volleyball Finals as the top university teams battle for the championship. The culmination of months of competition, this match promises thrilling exchanges, school spirit, and elite collegiate volleyball action. Support your alma mater or favorite university in this exciting sports event.',
            'longitude' => 120.9841,
            'latitude' => 14.5344,
            'category' => 'SPORTS'
        ],
        [
            'name' => 'Disney on Ice',
            'date' => '2025-06-25',
            'time' => '1:00PM - 3:00PM, 6:00PM - 8:00PM',
            'venue' => 'Smart Araneta',
            'address' => 'Cubao',
            'description' => 'Disney On Ice presents magical ice skating performances featuring beloved Disney characters. Watch as skilled skaters bring your favorite Disney stories to life through stunning choreography, colorful costumes, and memorable music. An enchanting experience for Disney fans of all ages that creates lasting family memories.',
            'longitude' => 121.0514,
            'latitude' => 14.6197,
            'category' => 'KIDS'
        ],
        [
            'name' => 'Kidzoona Play Festival',
            'date' => '2025-07-15',
            'time' => '10:00AM - 8:00PM',
            'venue' => 'SMX Convention',
            'address' => 'Pasay',
            'description' => 'The Kidzoona Play Festival transforms SMX Convention Center into the ultimate children\'s playground. With inflatable attractions, ball pits, interactive games, and creative zones, kids can play, explore, and have fun in a safe environment. Special appearances by children\'s characters and entertaining shows throughout the day make this a complete family event.',
            'longitude' => 120.9866,
            'latitude' => 14.5362,
            'category' => 'KIDS'
        ],
        [
            'name' => 'Philippine Philharmonic Orchestra',
            'date' => '2025-03-28',
            'time' => '8:00PM - 10:00PM',
            'venue' => 'Cultural Center',
            'address' => 'Manila',
            'description' => 'Experience the musical excellence of the Philippine Philharmonic Orchestra in this special concert featuring both classical masterpieces and Filipino compositions. Under the baton of their acclaimed conductor, the orchestra delivers a program that showcases technical brilliance and emotional depth. An evening of orchestral beauty in the iconic Cultural Center of the Philippines.',
            'longitude' => 120.9796,
            'latitude' => 14.5565,
            'category' => 'ARTS'
        ],
        [
            'name' => 'Art in the Park 2025',
            'date' => '2025-04-20',
            'time' => '10:00AM - 10:00PM',
            'venue' => 'Jaime Velasquez Park',
            'address' => 'Makati',
            'description' => 'Art in the Park transforms Jaime Velasquez Park into an outdoor art gallery featuring affordable artworks from leading and emerging Filipino artists. Browse paintings, sculptures, prints, and mixed media pieces in a relaxed park setting. With live music, food stalls, and interactive art activities, this annual event makes art accessible to everyone while supporting the local art community.',
            'longitude' => 121.0297,
            'latitude' => 14.5550,
            'category' => 'ARTS'
        ],
        [
            'name' => 'Manila Food Festival',
            'date' => '2025-03-25',
            'time' => '11:00AM - 10:00PM',
            'venue' => 'Bonifacio High Street',
            'address' => 'BGC',
            'description' => 'Savor the diverse flavors of the Philippines at the Manila Food Festival. This culinary celebration features food stalls from the country\'s best restaurants, street food vendors, and innovative chefs. Sample regional specialties, trendy fusion dishes, and classic Filipino favorites while enjoying cooking demonstrations, food competitions, and live entertainment throughout the day.',
            'longitude' => 121.0551,
            'latitude' => 14.5509,
            'category' => 'ARTS'
        ],
        [
            'name' => 'Summer Music Festival',
            'date' => '2025-05-10',
            'time' => '3:00PM - 1:00AM',
            'venue' => 'Clark Global City Park',
            'address' => 'Pampanga',
            'description' => 'The Summer Music Festival brings together top artists from multiple genres for a day-long celebration of music under the stars. With multiple stages, food concessions, art installations, and camping options, this festival offers a complete experience for music lovers. The lineup features both international acts and homegrown Filipino talent in a picturesque outdoor setting.',
            'longitude' => 120.5562,
            'latitude' => 15.1868,
            'category' => 'CONCERT'
        ],
        [
            'name' => 'Street Food Night Market',
            'date' => '2025-03-20',
            'time' => '5:00PM - 12:00AM',
            'venue' => 'Circuit Makati',
            'address' => 'Makati',
            'description' => 'Experience the vibrant street food culture of the Philippines at this lively night market. Dozens of vendors offer everything from classic Filipino street fare to international snacks with local twists. The market features atmospheric lighting, live acoustic performances, and communal dining areas, creating a festive social environment perfect for foodies and night owls.',
            'longitude' => 121.0153,
            'latitude' => 14.5652,
            'category' => 'ARTS'
        ],
        [
            'name' => 'TWICE x ONCE',
            'date' => '2025-04-15',
            'time' => '6:30PM - 9:30PM',
            'venue' => 'Mall Of Asia',
            'address' => 'Pasay',
            'description' => 'TWICE brings their electrifying performance to Manila! The globally acclaimed K-pop girl group will showcase their impressive repertoire of hits in this highly anticipated concert. ONCE (TWICE\'s official fandom) can look forward to spectacular choreography, stunning visuals, and the group\'s signature infectious energy in an unforgettable show that celebrates the special bond between TWICE and their fans.',
            'longitude' => 120.9841,
            'latitude' => 14.5344,
            'category' => 'CONCERT'
        ],
        // Additional events with updated dates
        [
            'name' => 'Blackpink World Tour: Born Pink',
            'date' => '2025-06-18',
            'time' => '7:00PM - 10:30PM',
            'venue' => 'Philippine Arena',
            'address' => 'Bulacan',
            'description' => 'Global K-pop sensation Blackpink returns to Manila with their spectacular Born Pink World Tour. Experience the phenomenal group\'s high-energy performances, stunning choreography, and hit songs that have broken records worldwide. This highly anticipated concert promises state-of-the-art production, incredible visuals, and the charismatic stage presence that has made Blackpink international superstars.',
            'longitude' => 120.9481,
            'latitude' => 14.7631,
            'category' => 'CONCERT'
        ],
        [
            'name' => 'Manila International Book Fair',
            'date' => '2025-09-12',
            'time' => '10:00AM - 8:00PM',
            'venue' => 'SMX Convention',
            'address' => 'Pasay',
            'description' => 'The Manila International Book Fair returns for its annual celebration of literature and publishing. Browse thousands of titles from local and international publishers, attend book signings by favorite authors, and participate in engaging literary discussions and workshops. With special sections for children\'s literature, academic books, and digital publishing, this event caters to readers of all ages and interests.',
            'longitude' => 120.9866,
            'latitude' => 14.5362,
            'category' => 'ARTS'
        ],
        [
            'name' => 'Broadway Manila: The Lion King',
            'date' => '2025-07-25',
            'time' => '8:00PM - 10:30PM',
            'venue' => 'Newport Performing Arts Theater',
            'address' => 'Pasay',
            'description' => 'Experience the magic of Disney\'s The Lion King as the acclaimed Broadway production makes its Philippine debut. This spectacular theatrical event brings to life the beloved story through stunning costumes, puppetry, and unforgettable music by Elton John and Tim Rice. The visionary direction and design transform the stage into the African savanna in this award-winning production that has captivated audiences worldwide.',
            'longitude' => 121.0213,
            'latitude' => 14.5172,
            'category' => 'ARTS'
        ],
        [
            'name' => 'Manila Coffee Festival',
            'date' => '2025-04-22',
            'time' => '9:00AM - 6:00PM',
            'venue' => 'World Trade Center',
            'address' => 'Pasay',
            'description' => 'Celebrate the rich coffee culture of the Philippines at the Manila Coffee Festival. This event showcases the finest local coffee producers, from small-batch specialty roasters to traditional coffee farmers from across the archipelago. Enjoy coffee tastings, barista competitions, brewing workshops, and discussions on sustainable coffee production. Discover the unique flavors and stories behind Philippine coffee in this caffeine-fueled celebration.',
            'longitude' => 120.9845,
            'latitude' => 14.5368,
            'category' => 'ARTS'
        ],
        [
            'name' => 'Philippines GameCon 2025',
            'date' => '2025-08-15',
            'time' => '10:00AM - 9:00PM',
            'venue' => 'SMX Convention',
            'address' => 'Pasay',
            'description' => 'Philippines GameCon brings together gamers, developers, and industry professionals for the country\'s premier gaming event. Experience the latest video game releases, esports tournaments with top teams, cosplay competitions, and panel discussions with game creators. With hands-on demo stations, retro gaming areas, and exclusive merchandise, this convention celebrates gaming culture in all its forms.',
            'longitude' => 120.9866,
            'latitude' => 14.5362,
            'category' => 'KIDS'
        ],
        [
            'name' => 'Ballet Philippines: Swan Lake',
            'date' => '2025-05-24',
            'time' => '7:30PM - 10:00PM',
            'venue' => 'Cultural Center',
            'address' => 'Manila',
            'description' => 'Ballet Philippines presents Tchaikovsky\'s masterpiece Swan Lake in a breathtaking production that showcases the company\'s artistic excellence. This timeless ballet tells the story of Prince Siegfried and Odette, a princess turned into a swan by an evil sorcerer\'s curse. With stunning choreography, elaborate costumes, and the iconic score performed by a live orchestra, this production brings classical ballet\'s most beloved work to life.',
            'longitude' => 120.9796,
            'latitude' => 14.5565,
            'category' => 'ARTS'
        ],
        [
            'name' => 'Filipino Indie Film Festival',
            'date' => '2025-08-05',
            'time' => '11:00AM - 11:00PM',
            'venue' => 'Glorietta Activity Center',
            'address' => 'Makati',
            'description' => 'Celebrating the vibrant independent cinema scene of the Philippines, this film festival showcases innovative storytelling from emerging and established Filipino filmmakers. The program features full-length features, short films, documentaries, and experimental works that explore diverse aspects of Filipino culture and contemporary issues. Q&A sessions with directors and actors provide insights into the creative process behind these compelling films.',
            'longitude' => 121.0264,
            'latitude' => 14.5531,
            'category' => 'ARTS'
        ],
        [
            'name' => 'Wanderland Music & Arts Festival',
            'date' => '2025-05-08',
            'time' => '12:00PM - 12:00AM',
            'venue' => 'Filinvest City Event Grounds',
            'address' => 'Alabang',
            'description' => 'Wanderland returns with its signature blend of international and local music acts across multiple genres. This two-day festival creates an immersive experience with art installations, interactive activities, and food vendors curated to complement the musical lineup. Known for its relaxed atmosphere and community vibe, Wanderland has become a highlight of the Philippine festival calendar for music lovers seeking discovery and connection.',
            'longitude' => 121.0414,
            'latitude' => 14.4172,
            'category' => 'CONCERT'
        ],
        [
            'name' => 'Manila Auto Show 2025',
            'date' => '2025-09-05',
            'time' => '10:00AM - 9:00PM',
            'venue' => 'World Trade Center',
            'address' => 'Pasay',
            'description' => 'The Manila Auto Show presents the latest vehicles, automotive technology, and aftermarket products in the Philippines\' largest motoring event. Major manufacturers unveil new models and concept cars alongside custom builds from local garages and restoration specialists. With test drive opportunities, driving simulators, and technical workshops, this show appeals to automotive enthusiasts, industry professionals, and families looking for their next vehicle.',
            'longitude' => 120.9845,
            'latitude' => 14.5368,
            'category' => 'ARTS'
        ],
        [
            'name' => 'Philippine Science Festival',
            'date' => '2025-07-10',
            'time' => '9:00AM - 6:00PM',
            'venue' => 'University of Santo Tomas',
            'address' => 'Manila',
            'description' => 'The Philippine Science Festival makes science accessible and exciting through interactive exhibits, demonstrations, and talks by leading researchers. Designed for curious minds of all ages, this event covers diverse scientific fields including astronomy, robotics, environmental science, and biotechnology. School groups, families, and science enthusiasts can participate in hands-on experiments and discover the wonders of scientific discovery in engaging ways.',
            'longitude' => 120.9873,
            'latitude' => 14.6099,
            'category' => 'KIDS'
        ],
        [
            'name' => 'Manila Fashion Week',
            'date' => '2025-08-15',
            'time' => 'Various Times',
            'venue' => 'Solaire Resort & Casino',
            'address' => 'Parañaque',
            'description' => 'Manila Fashion Week showcases the creativity and craftsmanship of Philippine designers alongside international fashion labels. Runway shows present collections ranging from haute couture to ready-to-wear, with special focus on sustainable fashion and traditional Filipino textiles. Industry panels, design exhibitions, and pop-up retail spaces make this a comprehensive celebration of style that highlights the Philippines\' position in the global fashion landscape.',
            'longitude' => 120.9798,
            'latitude' => 14.5203,
            'category' => 'ARTS'
        ],
        [
            'name' => 'BTS: Map of the Soul Tour',
            'date' => '2025-07-22',
            'time' => '6:30PM - 10:00PM',
            'venue' => 'Philippine Arena',
            'address' => 'Bulacan',
            'description' => 'Global phenomenon BTS brings their electrifying Map of the Soul Tour to Manila. The record-breaking K-pop group will perform their chart-topping hits with spectacular production, precision choreography, and the charismatic stage presence that has won them millions of fans worldwide. This highly anticipated concert promises an unforgettable experience for ARMY members and music fans alike.',
            'longitude' => 120.9481,
            'latitude' => 14.7631,
            'category' => 'CONCERT'
        ],
        [
            'name' => 'Philippine Independence Day Expo',
            'date' => '2025-06-12',
            'time' => '9:00AM - 6:00PM',
            'venue' => 'Manila Hotel',
            'address' => 'Manila',
            'description' => 'Commemorating Philippine Independence Day, this exhibition presents key moments in the nation\'s history through artifacts, documents, and interactive displays. From pre-colonial times through the Spanish and American periods to modern independence, the exhibits trace the evolution of Filipino identity and nationhood. Historians and cultural experts lead guided tours and discussions that bring the country\'s rich historical tapestry to life.',
            'longitude' => 120.9748,
            'latitude' => 14.5831,
            'category' => 'ARTS'
        ],
        [
            'name' => 'Philippine Craft Beer Festival',
            'date' => '2025-05-17',
            'time' => '4:00PM - 12:00AM',
            'venue' => 'Circuit Makati',
            'address' => 'Makati',
            'description' => 'Celebrate the thriving local craft beer scene at this festival featuring over 30 Philippine microbreweries. Sample a diverse range of styles from hoppy IPAs to rich stouts, many incorporating unique local ingredients and flavors. Brewers share their expertise in tasting sessions, while food pairings, live music, and brewing demonstrations create a festive atmosphere for beer enthusiasts and curious newcomers alike.',
            'longitude' => 121.0153,
            'latitude' => 14.5652,
            'category' => 'ARTS'
        ],
        [
            'name' => 'Cinemalaya Independent Film Festival',
            'date' => '2025-08-02',
            'time' => 'Various Times',
            'venue' => 'Cultural Center',
            'address' => 'Manila',
            'description' => 'Cinemalaya continues its mission of discovering and supporting fresh Filipino filmmaking talent with its annual showcase of independent cinema. Competition entries in short and feature-length categories present stories that push creative boundaries and reflect diverse perspectives on Philippine society. Retrospectives, tributes, and international selections round out the program for this essential event in the country\'s cultural calendar.',
            'longitude' => 120.9796,
            'latitude' => 14.5565,
            'category' => 'ARTS'
        ],
        [
            'name' => 'Tech Summit Philippines',
            'date' => '2025-07-20',
            'time' => '9:00AM - 5:00PM',
            'venue' => 'The Podium',
            'address' => 'Ortigas',
            'description' => 'The Tech Summit brings together industry leaders, startups, developers, and tech enthusiasts to explore emerging technologies and digital innovation. Conference tracks cover artificial intelligence, fintech, e-commerce, cybersecurity, and the startup ecosystem, with keynote speeches and panel discussions from local and international experts. Networking opportunities, product demonstrations, and a startup pitch competition make this the premier event for the Philippine tech community.',
            'longitude' => 121.0626,
            'latitude' => 14.5858,
            'category' => 'ARTS'
        ],
        [
            'name' => 'Summer Basketball Tournament',
            'date' => '2025-04-15',
            'time' => '4:00PM - 8:00PM',
            'venue' => 'Cuneta Astrodome',
            'address' => 'Pasay',
            'description' => 'The Summer Basketball Tournament sees the top college teams battle for championship glory. This tournament showcases the future stars of Philippine basketball in high-stakes games filled with skill, strategy, and school pride. With passionate fan bases creating an electric atmosphere, these games embody the competitive spirit and community connection that make collegiate sports a beloved national tradition.',
            'longitude' => 120.9947,
            'latitude' => 14.5391,
            'category' => 'SPORTS'
        ],
        [
            'name' => 'Summer Arts Festival',
            'date' => '2025-06-05',
            'time' => 'All Day',
            'venue' => 'Various Locations',
            'address' => 'Intramuros',
            'description' => 'The Summer Arts Festival transforms Intramuros into a vibrant celebration of Filipino creativity. Street performances, music competitions, art exhibitions, and cultural workshops fill the week-long event that showcases the talent and artistic vision of local creators. Elaborately designed installations and interactive exhibits create an immersive experience that draws visitors from across the Philippines and beyond.',
            'longitude' => 120.9748,
            'latitude' => 14.5890,
            'category' => 'ARTS'
        ],
        [
            'name' => 'Career Fair 2025',
            'date' => '2025-06-30',
            'time' => '9:00AM - 5:00PM',
            'venue' => 'Ayala Malls Manila Bay',
            'address' => 'Parañaque',
            'description' => 'The Career Fair connects job seekers with opportunities across multiple industries, featuring recruitment booths from over 100 companies. Beyond job applications, the event offers career development workshops, resume reviews, mock interviews, and guidance on professional growth. Special sections focus on opportunities for fresh graduates, overseas workers, and specialized technical fields, creating pathways to meaningful employment.',
            'longitude' => 120.9900,
            'latitude' => 14.5179,
            'category' => 'ARTS'
        ],
        [
            'name' => 'Ateneo vs La Salle: Basketball Rivalry',
            'date' => '2025-07-08',
            'time' => '4:00PM - 6:00PM',
            'venue' => 'Smart Araneta',
            'address' => 'Cubao',
            'description' => 'The legendary basketball rivalry between Ateneo and La Salle continues with another highly anticipated matchup. These games transcend sport to become cultural events that unite generations of alumni and highlight the unique traditions of both prestigious universities. With high-level basketball, spirited school bands, and coordinated cheering sections, this game promises to deliver the intensity and pageantry that have made it the premier rivalry in Philippine collegiate sports.',
            'longitude' => 121.0514,
            'latitude' => 14.6197,
            'category' => 'SPORTS'
        ],
        [
            'name' => 'Rainy Day Music Festival',
            'date' => '2025-08-15',
            'time' => '3:00PM - 10:00PM',
            'venue' => 'Greenfield District',
            'address' => 'Mandaluyong',
            'description' => 'The Rainy Day Music Festival brings together local indie artists for an intimate indoor music festival celebrating the rainy season. Featuring acoustic sets, folk music, and indie rock performances, this cozy event creates the perfect ambiance for the monsoon season. Warm food, hot drinks, and a marketplace of handcrafted items complete the experience that has become a beloved annual tradition for music lovers.',
            'longitude' => 121.0514,
            'latitude' => 14.5850,
            'category' => 'CONCERT'
        ]
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
     * Generate an image link for the event or merchandise.
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
        $storagePath = 'images/' . strtolower($type->value);
        
        // Create a unique filename
        $filename = Str::uuid() . '.png';
        
        // Copy the image to the storage path
        $imagePath = Storage::disk('public')->putFileAs(
            $storagePath, 
            new File($randomImage), 
            $filename
        );
        
        // Generate the URL for accessing the image using asset helper
        return asset("storage/{$imagePath}");
    }
}
