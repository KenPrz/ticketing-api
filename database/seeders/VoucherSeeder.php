<?php

namespace Database\Seeders;

use App\Enums\UserTypes;
use App\Models\User;
use App\Models\Voucher;
use Faker\Generator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VoucherSeeder extends Seeder
{
    /**
     * The discount values for the vouchers.
     * 
     * @var array<int>
     */
    private const DISCOUNT_VALUES = [
        100,
        200,
        300,
        400,
        500,
        600,
        700,
        800,
        900,
        1000,
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
        $organizers = User::where('user_type', UserTypes::ORGANIZER->value)->get();

        $organizers->each(function ($organizer) {
            $this->createVouchers($organizer);
        });
    }

    private function createVouchers($organizer)
    {
        $max = rand(10, 25);
        $isIndefinite = $this->faker->boolean(10);

        $vouchers = [];
        for ($i = 0; $i < $max; $i++) {
            $vouchers[] = [
                'name' => $this->faker->word(),
                'code' => strtoupper(Str::random(rand(5, 8))),
                'discount' => self::DISCOUNT_VALUES[array_rand(self::DISCOUNT_VALUES)],
                'start_date' => $isIndefinite 
                    ? null
                    : now()->subDays(10),
                'end_date' => $isIndefinite
                    ? null
                    : now()->addDays(10),
                'organizer_id' => $organizer->id,
            ];
        }

        Voucher::insert($vouchers);
    }
}
