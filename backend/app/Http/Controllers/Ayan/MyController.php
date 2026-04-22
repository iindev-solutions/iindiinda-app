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

class MyController extends Controller
{
    use SerializesAyanData;

    public function trips(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        return response()->json([
            'success' => true,
            'data' => Trip::query()
                ->with('driver')
                ->where('driver_id', $user->id)
                ->latest()
                ->get()
                ->map(fn (Trip $trip) => $this->serializeTrip($trip))
                ->values(),
        ]);
    }

    public function requests(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        return response()->json([
            'success' => true,
            'data' => AyanRequest::query()
                ->with('passenger')
                ->where('passenger_id', $user->id)
                ->latest()
                ->get()
                ->map(fn (AyanRequest $item) => $this->serializeRequest($item))
                ->values(),
        ]);
    }

    public function responses(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        return response()->json([
            'success' => true,
            'data' => AyanResponse::query()
                ->with('user')
                ->where('user_id', $user->id)
                ->latest()
                ->get()
                ->map(fn (AyanResponse $response) => $this->serializeResponse($response))
                ->values(),
        ]);
    }
}
