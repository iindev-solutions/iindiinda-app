<?php

namespace AppHttpControllers;

use IlluminateHttpJsonResponse;
use IlluminateHttpRequest;

class UserController extends Controller
{
    /**
     * Get current user
     *
     * Reply: User { id, telegram_id, username, first_name, role, created_at }
     */
    public function me(): JsonResponse
    {
        // TODO: Get from auth token (Sanctum)
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

        return response()->json($mockUser);
    }

    /**
     * Switch user role
     *
     * Body: { role: 'passenger' | 'driver' }
     * Reply: { user: User }
     */
    public function switchRole(Request $request): JsonResponse
    {
        $request->validate([
            'role' => 'required|in:passenger,driver,carrier,master,sender',
        ]);

        // TODO: Get from auth token, update in DB
        // For now, return mock with new role

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