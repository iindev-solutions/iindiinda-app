<?php

namespace App\Http\Controllers\Ayan;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Ayan\Concerns\SerializesAyanData;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TripController extends Controller
{
    use SerializesAyanData;

    public function index(Request $request): JsonResponse
    {
        $query = Trip::query()
            ->with('driver')
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

        $trips = $query->get()->map(fn (Trip $trip) => $this->serializeTrip($trip))->values();

        return response()->json([
            'success' => true,
            'data' => $trips,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        $validated = $request->validate([
            'from_address' => 'required|string|max:255',
            'to_address' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => ['required', 'regex:/^([01]\d|2[0-3]):([0-5]\d)$/'],
            'seats' => 'required|integer|min:1|max:10',
            'price' => 'required|integer|min:0',
            'comment' => 'nullable|string|max:500',
        ]);

        $trip = Trip::query()->create([
            'driver_id' => $user->id,
            'from_address' => $validated['from_address'],
            'to_address' => $validated['to_address'],
            'date' => $validated['date'],
            'time' => $validated['time'],
            'seats' => $validated['seats'],
            'price' => $validated['price'],
            'comment' => $validated['comment'] ?? null,
            'status' => 'open',
        ])->load('driver');

        return response()->json([
            'success' => true,
            'data' => $this->serializeTrip($trip),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $trip = Trip::query()->with('driver')->find($id);

        abort_if(!$trip, 404, 'Trip not found');

        return response()->json([
            'success' => true,
            'data' => $this->serializeTrip($trip),
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        $validated = $request->validate([
            'seats' => 'sometimes|integer|min:1|max:10',
            'price' => 'sometimes|integer|min:0',
            'comment' => 'nullable|string|max:500',
            'status' => 'sometimes|in:open,closed',
        ]);

        $trip = Trip::query()->with('driver')->find($id);

        abort_if(!$trip, 404, 'Trip not found');
        abort_if($trip->driver_id !== $user->id, 403, 'Forbidden');

        $trip->fill($validated);
        $trip->save();

        return response()->json([
            'success' => true,
            'data' => $this->serializeTrip($trip->fresh()->load('driver')),
        ]);
    }
}
