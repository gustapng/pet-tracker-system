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
        $user = User::factory()->create([
            'email' => 'teste@pettrack.com',
            'password' => bcrypt('senha123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'teste@pettrack.com',
            'password' => 'senha123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'access_token',
                     'token_type',
                     'expires_in'
                 ]);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'teste@pettrack.com',
            'password' => bcrypt('senha123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'teste@pettrack.com',
            'password' => 'senha-errada',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'error' => 'Unauthorized'
                 ]);
    }
}
