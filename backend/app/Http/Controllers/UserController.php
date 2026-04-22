<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        return response()->json($this->serializeUser($user));
    }

    public function switchRole(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'role' => 'required|in:passenger,driver,carrier,master,sender',
        ]);

        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        $user->role = $validated['role'];
        $user->save();

        return response()->json([
            'user' => $this->serializeUser($user->fresh()),
        ]);
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
