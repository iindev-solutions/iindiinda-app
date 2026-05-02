<?php

namespace App\Http\Controllers\Uus;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Uus\Concerns\SerializesUusData;
use App\Models\User;
use App\Models\UusResponse;
use App\Models\UusTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MyController extends Controller
{
    use SerializesUusData;

    public function tasks(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        return response()->json([
            'success' => true,
            'data' => UusTask::query()
                ->with('customer')
                ->where('customer_id', $user->id)
                ->latest()
                ->get()
                ->map(fn (UusTask $task) => $this->serializeTask($task))
                ->values(),
        ]);
    }

    public function responses(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        return response()->json([
            'success' => true,
            'data' => UusResponse::query()
                ->with(['user', 'task.customer'])
                ->where('user_id', $user->id)
                ->latest()
                ->get()
                ->map(fn (UusResponse $response) => $this->serializeResponse($response))
                ->values(),
        ]);
    }
}
