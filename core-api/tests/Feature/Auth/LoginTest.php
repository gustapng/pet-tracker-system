<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_correct_credentials(): void
    {
        // 1. Preparação (Arrange): Cria um usuário no banco de testes
        $user = User::factory()->create([
            'email' => 'teste@pettrack.com',
            'password' => bcrypt('senha123'),
        ]);

        // 2. Ação (Act): Faz um POST na rota de login com os dados corretos
        $response = $this->postJson('/api/auth/login', [
            'email' => 'teste@pettrack.com',
            'password' => 'senha123',
        ]);

        // 3. Verificação (Assert): Espera Sucesso (200) e a estrutura do Token JWT
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'access_token',
                     'token_type',
                     'expires_in'
                 ]);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        // 1. Preparação (Arrange)
        $user = User::factory()->create([
            'email' => 'teste@pettrack.com',
            'password' => bcrypt('senha123'),
        ]);

        // 2. Ação (Act): Tenta logar com a senha errada
        $response = $this->postJson('/api/auth/login', [
            'email' => 'teste@pettrack.com',
            'password' => 'senha-errada',
        ]);

        // 3. Verificação (Assert): Espera status 401 (Unauthorized)
        $response->assertStatus(401)
                 ->assertJson([
                     'error' => 'Unauthorized'
                 ]);
    }
}
