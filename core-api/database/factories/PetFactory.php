<?php

namespace Database\Factories;

use App\Models\Pet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pet>
 */
class PetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->firstName(),
            'breed' => fake()->randomElement(['Labrador', 'Pug', 'Beagle', 'Golden Retriever', 'SRD']),
            'age' => fake()->numberBetween(1, 15),
        ];
    }
}
