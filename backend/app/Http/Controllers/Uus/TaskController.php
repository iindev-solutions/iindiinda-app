<?php

namespace App\Http\Controllers\Uus;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Uus\Concerns\SerializesUusData;
use App\Models\User;
use App\Models\UusTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    use SerializesUusData;

    public function index(Request $request): JsonResponse
    {
        $query = UusTask::query()
            ->with('customer')
            ->where('status', 'open')
            ->latest();

        if ($request->filled('category')) {
            $query->where('category', (string) $request->string('category'));
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->string('location') . '%');
        }

        if ($request->filled('urgency')) {
            $query->where('urgency', (string) $request->string('urgency'));
        }

        if ($request->filled('desired_when')) {
            $query->where('desired_when', (string) $request->string('desired_when'));
        }

        if ($request->filled('date')) {
            $query->whereDate('date', (string) $request->string('date'));
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()->map(fn (UusTask $task) => $this->serializeTask($task))->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        $validated = $this->validateTaskPayload($request);

        $task = UusTask::query()->create([
            'customer_id' => $user->id,
            'category' => $validated['category'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'desired_when' => $validated['desired_when'],
            'date' => $this->resolveTaskDate($validated['desired_when'], $validated['date'] ?? null),
            'budget' => $validated['budget'] ?? null,
            'budget_type' => $validated['budget_type'],
            'urgency' => $validated['urgency'],
            'response_limit' => $this->resolveResponseLimit($validated['urgency']),
            'status' => 'open',
        ])->load('customer');

        return response()->json([
            'success' => true,
            'data' => $this->serializeTask($task),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $task = UusTask::query()->with('customer')->find($id);

        abort_if(!$task, 404, 'Task not found');

        return response()->json([
            'success' => true,
            'data' => $this->serializeTask($task),
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        $task = UusTask::query()->with('customer')->find($id);

        abort_if(!$task, 404, 'Task not found');
        abort_if($task->customer_id !== $user->id, 403, 'Forbidden');

        $validated = $this->validateTaskPayload($request, true);
        $nextStatus = $validated['status'] ?? null;

        if ($nextStatus !== null) {
            abort_if($nextStatus === 'matched', 422, 'Matched status is set by accepting a response');
            abort_if($task->status === 'matched' && $nextStatus === 'open', 422, 'Use completed or cancelled after matching');
            abort_if(in_array($task->status, ['completed', 'cancelled'], true) && $nextStatus !== $task->status, 422, 'Task is already final');
            abort_if($task->status === 'open' && $nextStatus === 'completed', 422, 'Task must be matched before completion');
        }

        $desiredWhen = $validated['desired_when'] ?? $task->desired_when;
        $explicitDate = array_key_exists('date', $validated) ? $validated['date'] : $task->date;
        $resolvedDate = $this->resolveTaskDate($desiredWhen, $explicitDate);

        if (array_key_exists('urgency', $validated)) {
            $nextLimit = $this->resolveResponseLimit($validated['urgency']);
            $responseCount = $task->responses()->count();

            abort_if($responseCount > $nextLimit, 422, 'Response limit would be lower than current response count');

            $validated['response_limit'] = $nextLimit;
        }

        $validated['desired_when'] = $desiredWhen;
        $validated['date'] = $resolvedDate;

        $task->fill($validated);
        $task->save();

        return response()->json([
            'success' => true,
            'data' => $this->serializeTask($task->fresh()->load('customer')),
        ]);
    }

    private function validateTaskPayload(Request $request, bool $partial = false): array
    {
        $validated = $request->validate([
            'category' => ($partial ? 'sometimes|' : '') . 'required|in:home,repair,delivery,other',
            'description' => ($partial ? 'sometimes|' : '') . 'required|string|max:500',
            'location' => ($partial ? 'sometimes|' : '') . 'required|string|max:255',
            'desired_when' => ($partial ? 'sometimes|' : '') . 'required|in:today,tomorrow,date,flexible',
            'date' => 'nullable|date|after_or_equal:today',
            'budget' => 'nullable|integer|min:0',
            'budget_type' => ($partial ? 'sometimes|' : '') . 'required|in:fixed,negotiable',
            'urgency' => ($partial ? 'sometimes|' : '') . 'required|in:urgent,normal',
            'status' => 'sometimes|in:open,matched,completed,cancelled',
        ]);

        $desiredWhen = $validated['desired_when'] ?? null;

        if ($desiredWhen === 'date' && (!array_key_exists('date', $validated) || $validated['date'] === null)) {
            throw ValidationException::withMessages([
                'date' => ['Date is required when desired_when is date'],
            ]);
        }

        return $validated;
    }

    private function resolveTaskDate(string $desiredWhen, ?string $explicitDate): ?string
    {
        return match ($desiredWhen) {
            'today' => now()->toDateString(),
            'tomorrow' => now()->addDay()->toDateString(),
            'date' => $explicitDate,
            'flexible' => null,
            default => $explicitDate,
        };
    }

    private function resolveResponseLimit(string $urgency): int
    {
        return $urgency === 'urgent' ? 3 : 5;
    }
}
