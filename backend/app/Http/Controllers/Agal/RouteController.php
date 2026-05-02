<?php

namespace App\Http\Controllers\Agal;

use App\Http\Controllers\Agal\Concerns\SerializesAgalData;
use App\Http\Controllers\Controller;
use App\Models\AgalRoute;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    use SerializesAgalData;

    public function index(Request $request): JsonResponse
    {
        $query = AgalRoute::query()
            ->with('carrier')
            ->where('status', 'open')
            ->upcomingForFeed()
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

        return response()->json([
            'success' => true,
            'data' => $query->get()->map(fn (AgalRoute $route) => $this->serializeRoute($route))->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');
        abort_if($user->role !== 'carrier', 403, 'Only carriers can create routes');

        $validated = $request->validate([
            'from_address' => 'required|string|max:255',
            'to_address' => 'required|string|max:255',
            'date' => 'required|date|after_or_equal:today',
            'time' => ['nullable', 'regex:/^([01]\d|2[0-3]):([0-5]\d)$/'],
            'size_label' => 'required|in:document,small,medium,large',
            'weight_kg_max' => 'nullable|numeric|gt:0|max:999999.99',
            'accepted_items' => 'nullable|string|max:500',
            'restricted_items' => 'nullable|string|max:500',
            'price' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $route = AgalRoute::query()->create([
            'carrier_id' => $user->id,
            'from_address' => $validated['from_address'],
            'to_address' => $validated['to_address'],
            'date' => $validated['date'],
            'time' => $validated['time'] ?? null,
            'size_label' => $validated['size_label'],
            'weight_kg_max' => $validated['weight_kg_max'] ?? null,
            'accepted_items' => $validated['accepted_items'] ?? null,
            'restricted_items' => $validated['restricted_items'] ?? null,
            'price' => $validated['price'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'open',
        ])->load('carrier');

        return response()->json([
            'success' => true,
            'data' => $this->serializeRoute($route),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $route = AgalRoute::query()->with('carrier')->find($id);

        abort_if(!$route, 404, 'Route not found');

        return response()->json([
            'success' => true,
            'data' => $this->serializeRoute($route),
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        $validated = $request->validate([
            'status' => 'sometimes|in:open,matched,completed,cancelled',
            'time' => ['nullable', 'regex:/^([01]\d|2[0-3]):([0-5]\d)$/'],
            'weight_kg_max' => 'nullable|numeric|gt:0|max:999999.99',
            'accepted_items' => 'nullable|string|max:500',
            'restricted_items' => 'nullable|string|max:500',
            'price' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $route = AgalRoute::query()->with('carrier')->find($id);

        abort_if(!$route, 404, 'Route not found');
        abort_if($route->carrier_id !== $user->id, 403, 'Forbidden');

        if (array_key_exists('status', $validated)) {
            $nextStatus = $validated['status'];

            abort_if($nextStatus === 'matched', 422, 'Matched status is set by accepting a response');
            abort_if($route->status === 'matched' && $nextStatus === 'open', 422, 'Use completed or cancelled after matching');
            abort_if(in_array($route->status, ['completed', 'cancelled'], true) && $nextStatus !== $route->status, 422, 'Route is already final');
            abort_if($route->status === 'open' && $nextStatus === 'completed', 422, 'Route must be matched before completion');
        }

        $route->fill($validated);
        $route->save();

        return response()->json([
            'success' => true,
            'data' => $this->serializeRoute($route->fresh()->load('carrier')),
        ]);
    }
}
