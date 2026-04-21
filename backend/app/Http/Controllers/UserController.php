<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function me(): JsonResponse
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

        return response()->json($mockUser);
    }

    public function switchRole(Request $request): JsonResponse
    {
        $request->validate([
            'role' => 'required|in:passenger,driver,carrier,master,sender',
        ]);

        $mockUser = [
            'id' => 1,
            'telegram_id' => 123456789,
            'username' => 'test_user',
            'first_name' => 'Тест',
            'role' => $request->input('role'),
            'rating' => 5.0,
            'completed_orders' => 0,
            'is_available' => true,
            'created_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
        ];

        return response()->json(['user' => $mockUser]);
    }
}
