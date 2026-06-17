<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pet;
use App\Models\SafeZone;
use App\Models\Alert;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Tech Lead Avaliador',
            'email' => 'admin@pettrack.com',
            'password' => Hash::make('senha123'),
        ]);

        $pets = Pet::factory(3)->create([
            'user_id' => $user->id,
        ]);

        foreach ($pets as $pet) {
            SafeZone::factory()->create([
                'pet_id' => $pet->id,
            ]);

            Alert::factory(5)->create([
                'pet_id' => $pet->id,
            ]);
        }
    }
}
