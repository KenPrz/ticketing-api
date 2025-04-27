<?php

namespace Database\Factories;

use App\Models\Message;
use App\Enums\ReadStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition()
    {
        return [
            'content' => $this->faker->sentence,
            'read_status' => $this->faker->randomElement(ReadStatus::getValues()),
        ];
    }
}
