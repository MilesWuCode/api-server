<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => \App\Models\User::all()->random()->id,
            'title' => $this->faker->text(rand(5, 200)),
            'body' => $this->faker->paragraph(),
            'status' => $this->faker->boolean(),
            'publish_at' => $this->faker->date(),
        ];
    }
}
