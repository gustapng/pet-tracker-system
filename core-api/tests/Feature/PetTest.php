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
        // 1. Arrange: Cria o usuário e o define como autenticado
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // Montamos o payload simulando o que o frontend (Vue.js) enviaria
        $payload = [
            'name' => 'Rex',
            'breed' => 'Golden Retriever',
            'age' => 3,
            'safe_zone' => [
                'latitude' => -16.3722, // Usando coordenadas aproximadas
                'longitude' => -39.5811,
                'radius_meters' => 100
            ]
        ];

        // 2. Act: Dispara o POST como se estivesse logado (actingAs)
        $response = $this->actingAs($user, 'api')->postJson('/api/pets', $payload);

        // 3. Assert: Exige status 201 (Created) e valida se salvou no banco
        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Rex']);

        // Verifica se o Pet realmente foi parar no banco amarrado ao usuário
        $this->assertDatabaseHas('pets', [
            'name' => 'Rex',
            'user_id' => $user->id,
        ]);

        // Verifica se a Zona de Segurança foi criada corretamente
        $this->assertDatabaseHas('safe_zones', [
            'latitude' => -16.3722,
            'radius_meters' => 100,
        ]);
    }

    public function test_it_validates_required_fields_to_create_pet(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // 2. Act: Envia um JSON completamente vazio
        $response = $this->actingAs($user, 'api')->postJson('/api/pets', []);

        // 3. Assert: Exige erro 422 (Erro de Validação)
        // E garante que a API reclame da falta do nome e das coordenadas
        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'name', 
                     'safe_zone.latitude', 
                     'safe_zone.longitude'
                 ]);
    }

    public function test_user_can_list_only_their_own_pets(): void
    {
        // 1. Arrange: Preparamos dois usuários diferentes
        /** @var \App\Models\User $user1 */
        $user1 = User::factory()->create();
        
        /** @var \App\Models\User $user2 */
        $user2 = User::factory()->create();

        // Criamos 2 pets para o usuário 1
        \App\Models\Pet::factory()->count(2)->create(['user_id' => $user1->id]);
        
        // Criamos 1 pet para o usuário 2 (esse pet NÃO deve aparecer na listagem)
        \App\Models\Pet::factory()->count(1)->create(['user_id' => $user2->id]);

        // 2. Act: Fazemos a requisição GET logados como o usuário 1
        $response = $this->actingAs($user1, 'api')->getJson('/api/pets');

        // 3. Assert: Esperamos sucesso (200) e que o JSON retorne exatamente 2 pets
        $response->assertStatus(200)
                 ->assertJsonCount(2);
    }
}
