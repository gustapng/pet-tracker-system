<?php

namespace Database\Factories;

use App\Models\SafeZone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SafeZone>
 */
class SafeZoneFactory extends Factory
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
            'radius_meters' => fake()->randomElement([50, 100, 200]),
        ];
    }
}
