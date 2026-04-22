<?php

namespace App\Http\Controllers\Ayan;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResponseController extends Controller
{
    public function tripIndex(int $tripId): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->sampleResponses($tripId, null),
        ]);
    }

    public function requestIndex(int $requestId): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->sampleResponses(null, $requestId),
        ]);
    }

    public function tripStore(Request $request, int $tripId): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'nullable|string|max:500',
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->makeResponse(999, $validated['message'] ?? null, $tripId, null),
        ], 201);
    }

    public function requestStore(Request $request, int $requestId): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'nullable|string|max:500',
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->makeResponse(999, $validated['message'] ?? null, null, $requestId),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        return response()->json([
            'success' => true,
            'data' => array_merge($this->makeResponse($id, null, 1, null), $validated),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $id,
                'deleted' => true,
            ],
        ]);
    }

    private function sampleResponses(?int $tripId, ?int $requestId): array
    {
        return [
            $this->makeResponse(1, 'Могу взять по пути', $tripId, $requestId),
            $this->makeResponse(2, 'Есть 2 свободных места', $tripId, $requestId),
        ];
    }

    private function makeResponse(int $id, ?string $message, ?int $tripId, ?int $requestId): array
    {
        return [
            'id' => $id,
            'trip_id' => $tripId,
            'request_id' => $requestId,
            'user' => [
                'id' => 10 + $id,
                'name' => 'Mock User ' . $id,
                'username' => 'mock_user_' . $id,
            ],
            'message' => $message,
            'status' => 'pending',
            'created_at' => now()->subMinutes($id * 5)->toIso8601String(),
        ];
    }
}
