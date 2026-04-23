<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Arr;
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
            if (!app()->environment(['local', 'testing'])) {
                throw ValidationException::withMessages([
                    'init_data' => 'Telegram user data is invalid.',
                ]);
            }

            return [
                'telegram_id' => 123456789,
                'first_name' => 'Тест',
                'username' => 'test_user',
            ];
        }

        $botToken = (string) config('services.telegram.bot_token', '');

        if ($botToken === '') {
            throw ValidationException::withMessages([
                'init_data' => 'Telegram auth is not configured.',
            ]);
        }

        parse_str($initData, $params);

        $hash = Arr::pull($params, 'hash');
        $authDate = $params['auth_date'] ?? null;

        if (!is_string($hash) || !is_numeric($authDate)) {
            throw ValidationException::withMessages([
                'init_data' => 'Telegram user data is invalid.',
            ]);
        }

        $timestamp = (int) $authDate;

        if ($timestamp < now()->subDay()->timestamp || $timestamp > now()->addMinutes(5)->timestamp) {
            throw ValidationException::withMessages([
                'init_data' => 'Telegram auth data expired.',
            ]);
        }

        ksort($params);

        $dataCheckString = collect($params)
            ->map(fn ($value, $key) => sprintf('%s=%s', $key, is_scalar($value) ? (string) $value : ''))
            ->implode("\n");

        $secretKey = hash_hmac('sha256', $botToken, 'WebAppData', true);
        $expectedHash = hash_hmac('sha256', $dataCheckString, $secretKey);

        if (!hash_equals($expectedHash, $hash)) {
            throw ValidationException::withMessages([
                'init_data' => 'Telegram user data is invalid.',
            ]);
        }

        $rawUser = $params['user'] ?? null;
        $decodedUser = is_string($rawUser) ? json_decode($rawUser, true) : null;

        $telegramId = $decodedUser['id'] ?? null;
        $firstName = $decodedUser['first_name'] ?? null;
        $username = $decodedUser['username'] ?? null;

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
