<?php

namespace App\Http\Controllers\Ayan;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $requests = array_values(array_filter($this->sampleRequests(), function (array $item) use ($request) {
            if ($request->filled('from') && stripos($item['from_address'], (string) $request->string('from')) === false) {
                return false;
            }

            if ($request->filled('to') && stripos($item['to_address'], (string) $request->string('to')) === false) {
                return false;
            }

            if ($request->filled('date') && $item['date'] !== (string) $request->string('date')) {
                return false;
            }

            return $item['status'] === 'open';
        }));

        return response()->json([
            'success' => true,
            'data' => $requests,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_address' => 'required|string|max:255',
            'to_address' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => ['nullable', 'regex:/^([01]\d|2[0-3]):([0-5]\d)$/'],
            'description' => 'nullable|string|max:500',
        ]);

        $item = [
            'id' => 999,
            'passenger' => $this->sampleUser(1, 'Тест Passenger', 'test_passenger'),
            'from_address' => $validated['from_address'],
            'to_address' => $validated['to_address'],
            'date' => $validated['date'],
            'time' => $validated['time'] ?? null,
            'description' => $validated['description'] ?? null,
            'status' => 'open',
            'created_at' => now()->toIso8601String(),
        ];

        return response()->json([
            'success' => true,
            'data' => $item,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $item = collect($this->sampleRequests())->firstWhere('id', $id);

        abort_if(!$item, 404, 'Request not found');

        return response()->json([
            'success' => true,
            'data' => $item,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:open,closed',
            'time' => ['nullable', 'regex:/^([01]\d|2[0-3]):([0-5]\d)$/'],
            'description' => 'nullable|string|max:500',
        ]);

        $item = collect($this->sampleRequests())->firstWhere('id', $id);

        abort_if(!$item, 404, 'Request not found');

        return response()->json([
            'success' => true,
            'data' => array_merge($item, $validated),
        ]);
    }

    private function sampleRequests(): array
    {
        return [
            [
                'id' => 1,
                'passenger' => $this->sampleUser(3, 'Айыына', 'aiyyna'),
                'from_address' => 'Якутск',
                'to_address' => 'Тулагино',
                'date' => now()->toDateString(),
                'time' => '18:30',
                'description' => 'Нужны 2 места',
                'status' => 'open',
                'created_at' => now()->subMinutes(40)->toIso8601String(),
            ],
            [
                'id' => 2,
                'passenger' => $this->sampleUser(4, 'Сардана', 'sardana'),
                'from_address' => 'Намцы',
                'to_address' => 'Якутск',
                'date' => now()->addDay()->toDateString(),
                'time' => null,
                'description' => null,
                'status' => 'open',
                'created_at' => now()->subHours(3)->toIso8601String(),
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
