<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceRecordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'identifier_1' => null,
            'identifier_2' => null,
            'identifier_3' => null,
            'identifier_4' => null,
            'identifier_5' => null,
            'data' => [],
        ];
    }

    public function forService(Service $service): static
    {
        return $this->state(fn (array $attributes) => [
            'service_id' => $service->id,
        ]);
    }

    public function withData(array $data): static
    {
        return $this->state(fn (array $attributes) => [
            'data' => $data,
        ]);
    }

    public function withIdentifiers(Service $service, array $data): static
    {
        $identifiers = [];

        foreach ($service->identifierFields as $field) {
            $column = 'identifier_'.$field->identifier_order;
            $identifiers[$column] = $data[$field->name] ?? null;
        }

        return $this->state(fn (array $attributes) => [
            'service_id' => $service->id,
            ...$identifiers,
            'data' => $data,
        ]);
    }
}
