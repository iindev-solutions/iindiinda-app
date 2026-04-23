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
        /** @var User|null $user */
        $user = request()->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        $trip = Trip::query()->find($tripId);

        abort_if(!$trip, 404, 'Trip not found');
        abort_if($trip->driver_id !== $user->id, 403, 'Forbidden');

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
        /** @var User|null $user */
        $user = request()->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        $ayanRequest = AyanRequest::query()->find($requestId);

        abort_if(!$ayanRequest, 404, 'Request not found');
        abort_if($ayanRequest->passenger_id !== $user->id, 403, 'Forbidden');

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
        abort_if($user->role !== 'passenger', 403, 'Only passengers can respond to trips');

        $validated = $request->validate([
            'message' => 'nullable|string|max:500',
        ]);

        $trip = Trip::query()->with('driver')->find($tripId);

        abort_if(!$trip, 404, 'Trip not found');
        abort_if($trip->status !== 'open', 422, 'Trip is closed');
        abort_if($trip->driver_id === $user->id, 422, 'Cannot respond to your own trip');
        abort_if(
            AyanResponse::query()->where('trip_id', $trip->id)->where('user_id', $user->id)->exists(),
            422,
            'You already responded to this trip'
        );

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
        abort_if($user->role !== 'driver', 403, 'Only drivers can respond to requests');

        $validated = $request->validate([
            'message' => 'nullable|string|max:500',
        ]);

        $ayanRequest = AyanRequest::query()->with('passenger')->find($requestId);

        abort_if(!$ayanRequest, 404, 'Request not found');
        abort_if($ayanRequest->status !== 'open', 422, 'Request is closed');
        abort_if($ayanRequest->passenger_id === $user->id, 422, 'Cannot respond to your own request');
        abort_if(
            AyanResponse::query()->where('request_id', $ayanRequest->id)->where('user_id', $user->id)->exists(),
            422,
            'You already responded to this request'
        );

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
            'status' => 'required|in:accepted,rejected',
        ]);

        $response = AyanResponse::query()->with(['user', 'trip.driver', 'request.passenger'])->find($id);

        abort_if(!$response, 404, 'Response not found');

        $tripOwnerId = $response->trip?->driver_id;
        $requestOwnerId = $response->request?->passenger_id;

        abort_if($tripOwnerId !== $user->id && $requestOwnerId !== $user->id, 403, 'Forbidden');
        abort_if($response->status !== 'pending', 422, 'Response is not pending');
        abort_if($response->trip && $response->trip->status !== 'open', 422, 'Trip is closed');
        abort_if($response->request && $response->request->status !== 'open', 422, 'Request is closed');

        if ($validated['status'] === 'accepted') {
            $conflictQuery = AyanResponse::query()->whereKeyNot($response->id)->where('status', 'accepted');

            if ($response->trip_id) {
                $conflictQuery->where('trip_id', $response->trip_id);
            } else {
                $conflictQuery->where('request_id', $response->request_id);
            }

            abort_if($conflictQuery->exists(), 422, 'Another response was already accepted');
        }

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
