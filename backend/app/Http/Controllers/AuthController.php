<?php

namespace AppHttpControllers;

use IlluminateHttpJsonResponse;

class AuthController extends Controller
{
    /**
     * Login via Telegram WebApp initData
     *
     * Body: { init_data: string }
     * Reply: { token: string, user: User }
     */
    public function loginViaTelegram(): JsonResponse
    {
        // TODO: Validate Telegram initData
        // For now, return mock user

        $mockUser = [
            'id' => 1,
            'telegram_id' => 123456789,
            'username' => 'test_user',
            'first_name' => 'Тест',
            'role' => 'passenger',
            'rating' => 5.0,
            'completed_orders' => 0,
            'is_available' => true,
            'created_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
        ];

        return response()->json([
            'token' => 'mock_token_' . uniqid(),
            'user' => $mockUser,
        ]);
    }
}