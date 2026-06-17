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
            'latitude' => fake()->latitude(-16.3000, -16.4000),
            'longitude' => fake()->longitude(-39.5000, -39.6000),
            'resolved' => fake()->boolean(30)
        ];
    }
}
