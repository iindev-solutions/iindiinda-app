<?php

namespace App\Http\Controllers\Ayan;

use App\Http\Controllers\Ayan\Concerns\SerializesAyanData;
use App\Http\Controllers\Controller;
use App\Models\AyanRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    use SerializesAyanData;

    public function index(Request $request): JsonResponse
    {
        $query = AyanRequest::query()
            ->with('passenger')
            ->where('status', 'open')
            ->latest();

        if ($request->filled('from')) {
            $query->where('from_address', 'like', '%' . $request->string('from') . '%');
        }

        if ($request->filled('to')) {
            $query->where('to_address', 'like', '%' . $request->string('to') . '%');
        }

        if ($request->filled('date')) {
            $query->whereDate('date', (string) $request->string('date'));
        }

        $requests = $query->get()->map(fn (AyanRequest $item) => $this->serializeRequest($item))->values();

        return response()->json([
            'success' => true,
            'data' => $requests,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');
        abort_if($user->role !== 'passenger', 403, 'Only passengers can create requests');

        $validated = $request->validate([
            'from_address' => 'required|string|max:255',
            'to_address' => 'required|string|max:255',
            'date' => 'required|date|after_or_equal:today',
            'time' => ['nullable', 'regex:/^([01]\d|2[0-3]):([0-5]\d)$/'],
            'description' => 'nullable|string|max:500',
        ]);

        $item = AyanRequest::query()->create([
            'passenger_id' => $user->id,
            'from_address' => $validated['from_address'],
            'to_address' => $validated['to_address'],
            'date' => $validated['date'],
            'time' => $validated['time'] ?? null,
            'description' => $validated['description'] ?? null,
            'status' => 'open',
        ])->load('passenger');

        return response()->json([
            'success' => true,
            'data' => $this->serializeRequest($item),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $item = AyanRequest::query()->with('passenger')->find($id);

        abort_if(!$item, 404, 'Request not found');

        return response()->json([
            'success' => true,
            'data' => $this->serializeRequest($item),
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        $validated = $request->validate([
            'status' => 'sometimes|in:open,closed',
            'time' => ['nullable', 'regex:/^([01]\d|2[0-3]):([0-5]\d)$/'],
            'description' => 'nullable|string|max:500',
        ]);

        $item = AyanRequest::query()->with('passenger')->find($id);

        abort_if(!$item, 404, 'Request not found');
        abort_if($item->passenger_id !== $user->id, 403, 'Forbidden');

        $item->fill($validated);
        $item->save();

        return response()->json([
            'success' => true,
            'data' => $this->serializeRequest($item->fresh()->load('passenger')),
        ]);
    }
}
