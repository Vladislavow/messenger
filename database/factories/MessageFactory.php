<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $sender = $this->faker->randomElement(array_column(User::all()->toArray(), 'id'));
        $recipient = $this->faker->randomElement(array_diff(array_column(User::all()->toArray(), 'id'), [$sender]));
        return [
            'sender' => $sender,
            'recipient' => $recipient,
            'content' => $this->faker->text()
        ];
    }
}
