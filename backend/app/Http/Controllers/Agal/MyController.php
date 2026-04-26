<?php

namespace App\Http\Controllers\Agal;

use App\Http\Controllers\Agal\Concerns\SerializesAgalData;
use App\Http\Controllers\Controller;
use App\Models\AgalRequest;
use App\Models\AgalResponse;
use App\Models\AgalRoute;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MyController extends Controller
{
    use SerializesAgalData;

    public function routes(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        return response()->json([
            'success' => true,
            'data' => AgalRoute::query()
                ->with('carrier')
                ->where('carrier_id', $user->id)
                ->latest()
                ->get()
                ->map(fn (AgalRoute $route) => $this->serializeRoute($route))
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
            'data' => AgalRequest::query()
                ->with('sender')
                ->where('sender_id', $user->id)
                ->latest()
                ->get()
                ->map(fn (AgalRequest $item) => $this->serializeRequest($item))
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
            'data' => AgalResponse::query()
                ->with(['user', 'route.carrier', 'request.sender'])
                ->where('user_id', $user->id)
                ->latest()
                ->get()
                ->map(fn (AgalResponse $response) => $this->serializeResponse($response))
                ->values(),
        ]);
    }
}
