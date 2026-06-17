<?php

namespace Database\Factories;

use App\Enums\FieldType;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFieldFactory extends Factory
{
    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'name' => fake()->slug(),
            'label' => fake()->words(2, true),
            'type' => FieldType::Text,
            'true_label' => null,
            'false_label' => null,
            'required' => false,
            'visible' => true,
            'is_identifier' => false,
            'identifier_order' => null,
            'sort_order' => fake()->numberBetween(1, 50),
        ];
    }
}
