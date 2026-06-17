<?php

namespace Database\Factories;

use App\Enums\SearchMode;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'slug' => fake()->slug(),
            'icon' => null,
            'description' => fake()->sentence(),
            'is_active' => true,
            'start_date' => null,
            'end_date' => null,
            'search_mode' => SearchMode::Or,
            'max_attempt' => 10,
            'attempt_duration' => 5,
            'sort_order' => fake()->unique()->numberBetween(1, 100),
        ];
    }
}
