<?php

namespace App\Http\Controllers\Ayan;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $trips = array_values(array_filter($this->sampleTrips(), function (array $trip) use ($request) {
            if ($request->filled('from') && stripos($trip['from_address'], (string) $request->string('from')) === false) {
                return false;
            }

            if ($request->filled('to') && stripos($trip['to_address'], (string) $request->string('to')) === false) {
                return false;
            }

            if ($request->filled('date') && $trip['date'] !== (string) $request->string('date')) {
                return false;
            }

            return $trip['status'] === 'open';
        }));

        return response()->json([
            'success' => true,
            'data' => $trips,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_address' => 'required|string|max:255',
            'to_address' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => ['required', 'regex:/^([01]\d|2[0-3]):([0-5]\d)$/'],
            'seats' => 'required|integer|min:1|max:10',
            'price' => 'required|integer|min:0',
            'comment' => 'nullable|string|max:500',
        ]);

        $trip = [
            'id' => 999,
            'driver' => $this->sampleUser(1, 'Тест Driver', 'test_driver'),
            'from_address' => $validated['from_address'],
            'to_address' => $validated['to_address'],
            'date' => $validated['date'],
            'time' => $validated['time'],
            'seats' => $validated['seats'],
            'price' => $validated['price'],
            'comment' => $validated['comment'] ?? null,
            'status' => 'open',
            'created_at' => now()->toIso8601String(),
        ];

        return response()->json([
            'success' => true,
            'data' => $trip,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $trip = collect($this->sampleTrips())->firstWhere('id', $id);

        abort_if(!$trip, 404, 'Trip not found');

        return response()->json([
            'success' => true,
            'data' => $trip,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'seats' => 'sometimes|integer|min:1|max:10',
            'price' => 'sometimes|integer|min:0',
            'comment' => 'nullable|string|max:500',
            'status' => 'sometimes|in:open,closed',
        ]);

        $trip = collect($this->sampleTrips())->firstWhere('id', $id);

        abort_if(!$trip, 404, 'Trip not found');

        return response()->json([
            'success' => true,
            'data' => array_merge($trip, $validated),
        ]);
    }

    private function sampleTrips(): array
    {
        return [
            [
                'id' => 1,
                'driver' => $this->sampleUser(1, 'Вася', 'vasya_driver'),
                'from_address' => 'Якутск',
                'to_address' => 'Намцы',
                'date' => now()->toDateString(),
                'time' => '09:00',
                'seats' => 3,
                'price' => 500,
                'comment' => 'Выезд утром',
                'status' => 'open',
                'created_at' => now()->subHours(2)->toIso8601String(),
            ],
            [
                'id' => 2,
                'driver' => $this->sampleUser(2, 'Петр', 'petya_driver'),
                'from_address' => 'Марха',
                'to_address' => 'Порт',
                'date' => now()->addDay()->toDateString(),
                'time' => '06:00',
                'seats' => 2,
                'price' => 200,
                'comment' => null,
                'status' => 'open',
                'created_at' => now()->subHour()->toIso8601String(),
            ],
        ];
    }

    private function sampleUser(int $id, string $name, ?string $username): array
    {
        return [
            'id' => $id,
            'name' => $name,
            'username' => $username,
        ];
    }
}
