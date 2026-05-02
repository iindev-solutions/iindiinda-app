<?php

namespace App\Http\Controllers\Uus;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Uus\Concerns\SerializesUusData;
use App\Models\User;
use App\Models\UusResponse;
use App\Models\UusTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ResponseController extends Controller
{
    use SerializesUusData;

    public function taskIndex(int $taskId): JsonResponse
    {
        /** @var User|null $user */
        $user = request()->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        $task = UusTask::query()->find($taskId);

        abort_if(!$task, 404, 'Task not found');
        abort_if($task->customer_id !== $user->id, 403, 'Forbidden');

        return response()->json([
            'success' => true,
            'data' => UusResponse::query()
                ->with('user')
                ->where('task_id', $taskId)
                ->latest()
                ->get()
                ->map(fn (UusResponse $response) => $this->serializeResponse($response))
                ->values(),
        ]);
    }

    public function taskStore(Request $request, int $taskId): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        $validated = $request->validate([
            'message' => 'nullable|string|max:500',
            'offered_price' => 'nullable|integer|min:0',
        ]);

        if (($validated['message'] ?? null) === null && !array_key_exists('offered_price', $validated)) {
            throw ValidationException::withMessages([
                'message' => ['Message or offered price is required'],
            ]);
        }

        if (($validated['message'] ?? null) === null && $validated['offered_price'] === null) {
            throw ValidationException::withMessages([
                'message' => ['Message or offered price is required'],
            ]);
        }

        $task = UusTask::query()->with('customer')->find($taskId);

        abort_if(!$task, 404, 'Task not found');
        abort_if($task->status !== 'open', 422, 'Task is closed');
        abort_if($task->customer_id === $user->id, 422, 'Cannot respond to your own task');
        abort_if(
            UusResponse::query()->where('task_id', $task->id)->where('user_id', $user->id)->exists(),
            422,
            'You already responded to this task'
        );
        abort_if(
            UusResponse::query()->where('task_id', $task->id)->count() >= $task->response_limit,
            422,
            'Response limit reached'
        );

        $response = UusResponse::query()->create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'message' => $validated['message'] ?? null,
            'offered_price' => $validated['offered_price'] ?? null,
            'status' => 'pending',
        ])->load('user');

        return response()->json([
            'success' => true,
            'data' => $this->serializeResponse($response),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        $response = UusResponse::query()->with(['user', 'task.customer'])->find($id);

        abort_if(!$response, 404, 'Response not found');
        abort_if($response->task?->customer_id !== $user->id, 403, 'Forbidden');
        abort_if($response->status !== 'pending', 422, 'Response is not pending');
        abort_if($response->task && $response->task->status !== 'open', 422, 'Task is not open');

        if ($validated['status'] === 'accepted') {
            $conflictQuery = UusResponse::query()
                ->where('task_id', $response->task_id)
                ->whereKeyNot($response->id)
                ->where('status', 'accepted');

            abort_if($conflictQuery->exists(), 422, 'Another response was already accepted');
        }

        DB::transaction(function () use ($response, $validated) {
            $response->status = $validated['status'];
            $response->save();

            if ($validated['status'] !== 'accepted') {
                return;
            }

            UusTask::query()->whereKey($response->task_id)->update(['status' => 'matched']);
            UusResponse::query()
                ->where('task_id', $response->task_id)
                ->whereKeyNot($response->id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);
        });

        return response()->json([
            'success' => true,
            'data' => $this->serializeResponse($response->fresh()->load('user')),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        $response = UusResponse::query()->find($id);

        abort_if(!$response, 404, 'Response not found');
        abort_if($response->user_id !== $user->id, 403, 'Forbidden');
        abort_if($response->status !== 'pending', 422, 'Only pending responses can be deleted');

        $response->delete();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $id,
                'deleted' => true,
            ],
        ]);
    }
}
