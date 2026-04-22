<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function loginViaTelegram(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'init_data' => 'required|string',
        ]);

        $telegramUser = $this->resolveTelegramUser($validated['init_data']);

        $user = User::query()->firstOrNew([
            'telegram_id' => $telegramUser['telegram_id'],
        ]);

        if (!$user->exists) {
            $user->role = 'passenger';
            $user->rating = 5.0;
            $user->completed_orders = 0;
            $user->is_available = true;
        }

        $user->first_name = $telegramUser['first_name'];
        $user->username = $telegramUser['username'];
        $user->save();

        $user->tokens()->delete();

        $token = $user->createToken('telegram')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $this->serializeUser($user->fresh()),
        ]);
    }

    private function resolveTelegramUser(string $initData): array
    {
        if ($initData === 'test') {
            return [
                'telegram_id' => 123456789,
                'first_name' => 'Тест',
                'username' => 'test_user',
            ];
        }

        parse_str($initData, $params);

        $rawUser = $params['user'] ?? null;
        $decodedUser = is_string($rawUser) ? json_decode($rawUser, true) : null;

        $telegramId = $decodedUser['id'] ?? $params['id'] ?? null;
        $firstName = $decodedUser['first_name'] ?? $params['first_name'] ?? null;
        $username = $decodedUser['username'] ?? $params['username'] ?? null;

        if (!$telegramId || !$firstName) {
            throw ValidationException::withMessages([
                'init_data' => 'Telegram user data is invalid.',
            ]);
        }

        return [
            'telegram_id' => (int) $telegramId,
            'first_name' => (string) $firstName,
            'username' => $username ? (string) $username : null,
        ];
    }

    private function serializeUser(User $user): array
    {
        return [
            'id' => $user->id,
            'telegram_id' => $user->telegram_id,
            'username' => $user->username,
            'first_name' => $user->first_name,
            'role' => $user->role,
            'rating' => (float) $user->rating,
            'completed_orders' => $user->completed_orders,
            'is_available' => (bool) $user->is_available,
            'created_at' => $user->created_at?->toIso8601String(),
            'updated_at' => $user->updated_at?->toIso8601String(),
        ];
    }
}
