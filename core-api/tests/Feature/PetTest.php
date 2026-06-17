<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PetTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_a_pet_with_safe_zone(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $payload = [
            'name' => 'Rex',
            'breed' => 'Golden Retriever',
            'age' => 3,
            'safe_zone' => [
                'latitude' => -16.3722,
                'longitude' => -39.5811,
                'radius_meters' => 100
            ]
        ];

        $response = $this->actingAs($user, 'api')->postJson('/api/pets', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Rex']);

        $this->assertDatabaseHas('pets', [
            'name' => 'Rex',
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('safe_zones', [
            'latitude' => -16.3722,
            'radius_meters' => 100,
        ]);
    }

    public function test_it_validates_required_fields_to_create_pet(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson('/api/pets', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'name', 
                     'safe_zone.latitude', 
                     'safe_zone.longitude'
                 ]);
    }

    public function test_user_can_list_only_their_own_pets(): void
    {
        /** @var \App\Models\User $user1 */
        $user1 = User::factory()->create();
        
        /** @var \App\Models\User $user2 */
        $user2 = User::factory()->create();

        \App\Models\Pet::factory()->count(2)->create(['user_id' => $user1->id]);
        
        \App\Models\Pet::factory()->count(1)->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1, 'api')->getJson('/api/pets');

        $response->assertStatus(200)
                 ->assertJsonCount(2);
    }
}
