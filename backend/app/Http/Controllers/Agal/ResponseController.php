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
use Illuminate\Support\Facades\DB;

class ResponseController extends Controller
{
    use SerializesAgalData;

    public function routeIndex(int $routeId): JsonResponse
    {
        /** @var User|null $user */
        $user = request()->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        $route = AgalRoute::query()->find($routeId);

        abort_if(!$route, 404, 'Route not found');
        abort_if($route->carrier_id !== $user->id, 403, 'Forbidden');

        return response()->json([
            'success' => true,
            'data' => AgalResponse::query()
                ->with('user')
                ->where('route_id', $routeId)
                ->latest()
                ->get()
                ->map(fn (AgalResponse $response) => $this->serializeResponse($response))
                ->values(),
        ]);
    }

    public function requestIndex(int $requestId): JsonResponse
    {
        /** @var User|null $user */
        $user = request()->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        $item = AgalRequest::query()->find($requestId);

        abort_if(!$item, 404, 'Request not found');
        abort_if($item->sender_id !== $user->id, 403, 'Forbidden');

        return response()->json([
            'success' => true,
            'data' => AgalResponse::query()
                ->with('user')
                ->where('request_id', $requestId)
                ->latest()
                ->get()
                ->map(fn (AgalResponse $response) => $this->serializeResponse($response))
                ->values(),
        ]);
    }

    public function routeStore(Request $request, int $routeId): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');
        abort_if($user->role !== 'sender', 403, 'Only senders can respond to routes');

        $validated = $request->validate([
            'message' => 'nullable|string|max:500',
        ]);

        $route = AgalRoute::query()->with('carrier')->find($routeId);

        abort_if(!$route, 404, 'Route not found');
        abort_if($route->status !== 'open', 422, 'Route is closed');
        abort_if($route->isPast(), 422, 'Route is past');
        abort_if($route->carrier_id === $user->id, 422, 'Cannot respond to your own route');
        abort_if(
            AgalResponse::query()->where('route_id', $route->id)->where('user_id', $user->id)->exists(),
            422,
            'You already responded to this route'
        );

        $response = AgalResponse::query()->create([
            'user_id' => $user->id,
            'route_id' => $route->id,
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
        abort_if($user->role !== 'carrier', 403, 'Only carriers can respond to requests');

        $validated = $request->validate([
            'message' => 'nullable|string|max:500',
        ]);

        $item = AgalRequest::query()->with('sender')->find($requestId);

        abort_if(!$item, 404, 'Request not found');
        abort_if($item->status !== 'open', 422, 'Request is closed');
        abort_if($item->isPast(), 422, 'Request is past');
        abort_if($item->sender_id === $user->id, 422, 'Cannot respond to your own request');
        abort_if(
            AgalResponse::query()->where('request_id', $item->id)->where('user_id', $user->id)->exists(),
            422,
            'You already responded to this request'
        );

        $response = AgalResponse::query()->create([
            'user_id' => $user->id,
            'route_id' => null,
            'request_id' => $item->id,
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

        $response = AgalResponse::query()->with(['user', 'route.carrier', 'request.sender'])->find($id);

        abort_if(!$response, 404, 'Response not found');

        $routeOwnerId = $response->route?->carrier_id;
        $requestOwnerId = $response->request?->sender_id;

        abort_if($routeOwnerId !== $user->id && $requestOwnerId !== $user->id, 403, 'Forbidden');
        abort_if($response->status !== 'pending', 422, 'Response is not pending');
        abort_if($response->route && $response->route->status !== 'open', 422, 'Route is not open');
        abort_if($response->request && $response->request->status !== 'open', 422, 'Request is not open');
        abort_if($response->route && $response->route->isPast(), 422, 'Route is past');
        abort_if($response->request && $response->request->isPast(), 422, 'Request is past');

        if ($validated['status'] === 'accepted') {
            $conflictQuery = AgalResponse::query()->whereKeyNot($response->id)->where('status', 'accepted');

            if ($response->route_id) {
                $conflictQuery->where('route_id', $response->route_id);
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

            if ($response->route_id) {
                AgalRoute::query()->whereKey($response->route_id)->update(['status' => 'matched']);
                AgalResponse::query()
                    ->where('route_id', $response->route_id)
                    ->whereKeyNot($response->id)
                    ->where('status', 'pending')
                    ->update(['status' => 'rejected']);

                return;
            }

            AgalRequest::query()->whereKey($response->request_id)->update(['status' => 'matched']);
            AgalResponse::query()
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

        $response = AgalResponse::query()->find($id);

        abort_if(!$response, 404, 'Response not found');
        abort_if($response->user_id !== $user->id, 403, 'Forbidden');
        abort_if($response->status !== 'pending', 422, 'Only pending responses can be deleted');

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
