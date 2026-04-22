<?php

namespace App\Http\Controllers\Ayan;

use App\Http\Controllers\Ayan\Concerns\SerializesAyanData;
use App\Http\Controllers\Controller;
use App\Models\AyanRequest;
use App\Models\AyanResponse;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResponseController extends Controller
{
    use SerializesAyanData;

    public function tripIndex(int $tripId): JsonResponse
    {
        abort_unless(Trip::query()->whereKey($tripId)->exists(), 404, 'Trip not found');

        return response()->json([
            'success' => true,
            'data' => AyanResponse::query()
                ->with('user')
                ->where('trip_id', $tripId)
                ->latest()
                ->get()
                ->map(fn (AyanResponse $response) => $this->serializeResponse($response))
                ->values(),
        ]);
    }

    public function requestIndex(int $requestId): JsonResponse
    {
        abort_unless(AyanRequest::query()->whereKey($requestId)->exists(), 404, 'Request not found');

        return response()->json([
            'success' => true,
            'data' => AyanResponse::query()
                ->with('user')
                ->where('request_id', $requestId)
                ->latest()
                ->get()
                ->map(fn (AyanResponse $response) => $this->serializeResponse($response))
                ->values(),
        ]);
    }

    public function tripStore(Request $request, int $tripId): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        $validated = $request->validate([
            'message' => 'nullable|string|max:500',
        ]);

        $trip = Trip::query()->with('driver')->find($tripId);

        abort_if(!$trip, 404, 'Trip not found');
        abort_if($trip->driver_id === $user->id, 422, 'Cannot respond to your own trip');

        $response = AyanResponse::query()->create([
            'user_id' => $user->id,
            'trip_id' => $trip->id,
            'request_id' => null,
            'message' => $validated['message'] ?? null,
            'status' => 'pending',
        ])->load('user');

        return response()->json([
            'success' => true,
            'data' => $this->serializeResponse($response),
        ], 201);
    }

    public function requestStore(Request $request, int $requestId): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        $validated = $request->validate([
            'message' => 'nullable|string|max:500',
        ]);

        $ayanRequest = AyanRequest::query()->with('passenger')->find($requestId);

        abort_if(!$ayanRequest, 404, 'Request not found');
        abort_if($ayanRequest->passenger_id === $user->id, 422, 'Cannot respond to your own request');

        $response = AyanResponse::query()->create([
            'user_id' => $user->id,
            'trip_id' => null,
            'request_id' => $ayanRequest->id,
            'message' => $validated['message'] ?? null,
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
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        $response = AyanResponse::query()->with(['user', 'trip.driver', 'request.passenger'])->find($id);

        abort_if(!$response, 404, 'Response not found');

        $tripOwnerId = $response->trip?->driver_id;
        $requestOwnerId = $response->request?->passenger_id;

        abort_if($tripOwnerId !== $user->id && $requestOwnerId !== $user->id, 403, 'Forbidden');

        DB::transaction(function () use ($response, $validated) {
            $response->status = $validated['status'];
            $response->save();

            if ($validated['status'] !== 'accepted') {
                return;
            }

            if ($response->trip_id) {
                Trip::query()->whereKey($response->trip_id)->update(['status' => 'closed']);
                AyanResponse::query()
                    ->where('trip_id', $response->trip_id)
                    ->whereKeyNot($response->id)
                    ->where('status', 'pending')
                    ->update(['status' => 'rejected']);

                return;
            }

            AyanRequest::query()->whereKey($response->request_id)->update(['status' => 'closed']);
            AyanResponse::query()
                ->where('request_id', $response->request_id)
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

        $response = AyanResponse::query()->find($id);

        abort_if(!$response, 404, 'Response not found');
        abort_if($response->user_id !== $user->id, 403, 'Forbidden');

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
