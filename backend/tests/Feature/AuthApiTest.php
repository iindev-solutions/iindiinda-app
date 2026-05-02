<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        $this->app['config']->set('services.telegram.bot_token', null);

        parent::tearDown();
    }

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

    public function test_valid_signed_init_data_is_accepted(): void
    {
        $this->setTelegramBotToken('bot-token');

        $response = $this->postJson('/api/auth/telegram', [
            'init_data' => $this->makeSignedInitData([
                'id' => 444555666,
                'first_name' => 'Signed',
                'username' => 'signed_user',
            ]),
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('user.telegram_id', 444555666)
            ->assertJsonPath('user.first_name', 'Signed')
            ->assertJsonPath('user.username', 'signed_user');
    }

    public function test_invalid_signed_init_data_is_rejected(): void
    {
        $this->setTelegramBotToken('bot-token');

        $response = $this->postJson('/api/auth/telegram', [
            'init_data' => 'user=%7B%22id%22%3A1%2C%22first_name%22%3A%22Fake%22%7D',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors('init_data');
    }

    public function test_test_init_data_is_rejected_outside_local_and_testing(): void
    {
        $originalEnv = $this->app['env'];
        $this->app['env'] = 'production';

        try {
            $response = $this->postJson('/api/auth/telegram', [
                'init_data' => 'test',
            ]);

            $response
                ->assertStatus(422)
                ->assertJsonValidationErrors('init_data');
        } finally {
            $this->app['env'] = $originalEnv;
        }
    }

    private function setTelegramBotToken(string $token): void
    {
        $this->app['config']->set('services.telegram.bot_token', $token);
    }

    private function makeSignedInitData(array $user): string
    {
        $authDate = (string) now()->timestamp;
        $userJson = json_encode($user, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $fields = [
            'auth_date' => $authDate,
            'query_id' => 'AAEAAAE',
            'user' => $userJson,
        ];

        ksort($fields);

        $dataCheckString = collect($fields)
            ->map(fn ($value, $key) => sprintf('%s=%s', $key, $value))
            ->implode("\n");

        $secretKey = hash_hmac('sha256', (string) config('services.telegram.bot_token'), 'WebAppData', true);
        $hash = hash_hmac('sha256', $dataCheckString, $secretKey);

        $fields['hash'] = $hash;

        return http_build_query($fields, '', '&', PHP_QUERY_RFC3986);
    }
}
