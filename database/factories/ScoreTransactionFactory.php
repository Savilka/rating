<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ScoreTransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'score'      => fake()->numberBetween(1, 100),
            'created_at' => fake()->dateTimeBetween('-10 days')->format('Y-m-d H:i:s'),
        ];
    }
}
