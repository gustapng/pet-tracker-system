<?php

namespace Database\Factories;

use App\Models\Alert;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Alert>
 */
class AlertFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'latitude' => fake()->latitude(-23.588333, -23.688333),
            'longitude' => fake()->longitude(-46.658890, -46.758890),
            'resolved' => fake()->boolean(30)
        ];
    }
}
