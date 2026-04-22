<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function loginViaTelegram(): JsonResponse
    {
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
