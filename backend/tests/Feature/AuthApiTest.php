<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_telegram_login_creates_user_and_returns_api_token(): void
    {
        $response = $this->postJson('/api/auth/telegram', [
            'init_data' => 'test'
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'token',
                'user' => [
                    'id',
                    'telegram_id',
                    'first_name',
                    'username',
                    'role',
                    'rating',
                    'completed_orders',
                    'is_available',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertNotSame('mock_token_', substr((string) $response->json('token'), 0, 11));
        $this->assertDatabaseHas('users', [
            'telegram_id' => 123456789,
            'first_name' => 'Тест',
            'role' => 'passenger'
        ]);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_current_user_endpoint_returns_authenticated_user(): void
    {
        $user = User::create([
            'telegram_id' => 777888999,
            'first_name' => 'Ayan',
            'username' => 'ayan_user',
            'role' => 'driver',
            'rating' => 5.0,
            'completed_orders' => 2,
            'is_available' => true
        ]);

        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/user');

        $response
            ->assertOk()
            ->assertJson([
                'id' => $user->id,
                'telegram_id' => $user->telegram_id,
                'first_name' => $user->first_name,
                'username' => $user->username,
                'role' => $user->role
            ]);
    }
}
