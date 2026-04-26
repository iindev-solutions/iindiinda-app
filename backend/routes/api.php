<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Ayan\MyController;
use App\Http\Controllers\Ayan\RequestController;
use App\Http\Controllers\Ayan\ResponseController;
use App\Http\Controllers\Ayan\TripController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'version' => '0.1.0']);
});

Route::post('/auth/telegram', [AuthController::class, 'loginViaTelegram']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'me']);
    Route::post('/user/switch-role', [UserController::class, 'switchRole']);

    Route::get('/ayan/trips', [TripController::class, 'index']);
    Route::post('/ayan/trips', [TripController::class, 'store']);
    Route::get('/ayan/trips/{id}', [TripController::class, 'show']);
    Route::patch('/ayan/trips/{id}', [TripController::class, 'update']);

    Route::get('/ayan/requests', [RequestController::class, 'index']);
    Route::post('/ayan/requests', [RequestController::class, 'store']);
    Route::get('/ayan/requests/{id}', [RequestController::class, 'show']);
    Route::patch('/ayan/requests/{id}', [RequestController::class, 'update']);

    Route::get('/ayan/trips/{id}/responses', [ResponseController::class, 'tripIndex']);
    Route::get('/ayan/requests/{id}/responses', [ResponseController::class, 'requestIndex']);
    Route::post('/ayan/trips/{id}/responses', [ResponseController::class, 'tripStore']);
    Route::post('/ayan/requests/{id}/responses', [ResponseController::class, 'requestStore']);
    Route::patch('/ayan/responses/{id}', [ResponseController::class, 'update']);
    Route::delete('/ayan/responses/{id}', [ResponseController::class, 'destroy']);

    Route::get('/ayan/my/trips', [MyController::class, 'trips']);
    Route::get('/ayan/my/requests', [MyController::class, 'requests']);
    Route::get('/ayan/my/responses', [MyController::class, 'responses']);

    Route::get('/tal/services', fn () => response()->json([]));
    Route::get('/tal/masters', fn () => response()->json([]));
    Route::get('/tal/slots', fn () => response()->json([]));
    Route::post('/tal/bookings', fn () => response()->json([], 201));
    Route::delete('/tal/bookings/{id}', fn () => response()->json([], 204));

    Route::post('/uus/tasks', fn () => response()->json([], 201));
    Route::get('/uus/tasks/open', fn () => response()->json([]));
    Route::post('/uus/tasks/{id}/respond', fn () => response()->json([], 201));

    Route::get('/agal/routes', fn () => response()->json(['success' => true, 'data' => []]));
    Route::post('/agal/routes', function (Request $request) {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => 0,
                'carrier' => [
                    'id' => $user?->id ?? 0,
                    'name' => $user?->first_name ?? 'User',
                    'username' => $user?->username,
                ],
                'from_address' => (string) $request->input('from_address', ''),
                'to_address' => (string) $request->input('to_address', ''),
                'date' => (string) $request->input('date', now()->toDateString()),
                'time' => $request->input('time'),
                'size_label' => (string) $request->input('size_label', 'small'),
                'weight_kg_max' => $request->input('weight_kg_max'),
                'accepted_items' => $request->input('accepted_items'),
                'restricted_items' => $request->input('restricted_items'),
                'price' => $request->input('price'),
                'notes' => $request->input('notes'),
                'status' => 'open',
                'created_at' => now()->toIso8601String(),
            ],
        ], 201);
    });
    Route::get('/agal/routes/{id}', fn () => response()->json(['success' => false, 'message' => 'AGAL route detail not implemented yet'], 501));
    Route::patch('/agal/routes/{id}', fn () => response()->json(['success' => false, 'message' => 'AGAL route update not implemented yet'], 501));

    Route::get('/agal/requests', fn () => response()->json(['success' => true, 'data' => []]));
    Route::post('/agal/requests', function (Request $request) {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => 0,
                'sender' => [
                    'id' => $user?->id ?? 0,
                    'name' => $user?->first_name ?? 'User',
                    'username' => $user?->username,
                ],
                'from_address' => (string) $request->input('from_address', ''),
                'to_address' => (string) $request->input('to_address', ''),
                'date' => (string) $request->input('date', now()->toDateString()),
                'time' => $request->input('time'),
                'size_label' => (string) $request->input('size_label', 'small'),
                'weight_kg' => $request->input('weight_kg'),
                'contents_summary' => (string) $request->input('contents_summary', ''),
                'fragility' => (string) $request->input('fragility', 'normal'),
                'documents_required' => (bool) $request->input('documents_required', false),
                'budget' => $request->input('budget'),
                'notes' => $request->input('notes'),
                'status' => 'open',
                'created_at' => now()->toIso8601String(),
            ],
        ], 201);
    });
    Route::get('/agal/requests/{id}', fn () => response()->json(['success' => false, 'message' => 'AGAL request detail not implemented yet'], 501));
    Route::patch('/agal/requests/{id}', fn () => response()->json(['success' => false, 'message' => 'AGAL request update not implemented yet'], 501));

    Route::get('/agal/routes/{id}/responses', fn () => response()->json(['success' => true, 'data' => []]));
    Route::post('/agal/routes/{id}/responses', function (Request $request, int $id) {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => 0,
                'route_id' => $id,
                'request_id' => null,
                'route' => null,
                'request' => null,
                'user' => [
                    'id' => $user?->id ?? 0,
                    'name' => $user?->first_name ?? 'User',
                    'username' => $user?->username,
                ],
                'message' => $request->input('message'),
                'status' => 'pending',
                'created_at' => now()->toIso8601String(),
            ],
        ], 201);
    });
    Route::get('/agal/requests/{id}/responses', fn () => response()->json(['success' => true, 'data' => []]));
    Route::post('/agal/requests/{id}/responses', function (Request $request, int $id) {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => 0,
                'route_id' => null,
                'request_id' => $id,
                'route' => null,
                'request' => null,
                'user' => [
                    'id' => $user?->id ?? 0,
                    'name' => $user?->first_name ?? 'User',
                    'username' => $user?->username,
                ],
                'message' => $request->input('message'),
                'status' => 'pending',
                'created_at' => now()->toIso8601String(),
            ],
        ], 201);
    });
    Route::patch('/agal/responses/{id}', function (Request $request, int $id) {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $id,
                'route_id' => null,
                'request_id' => null,
                'route' => null,
                'request' => null,
                'user' => [
                    'id' => $user?->id ?? 0,
                    'name' => $user?->first_name ?? 'User',
                    'username' => $user?->username,
                ],
                'message' => null,
                'status' => (string) $request->input('status', 'pending'),
                'created_at' => now()->toIso8601String(),
            ],
        ]);
    });
    Route::delete('/agal/responses/{id}', function (int $id) {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $id,
                'deleted' => true,
            ],
        ]);
    });

    Route::get('/agal/my/routes', fn () => response()->json(['success' => true, 'data' => []]));
    Route::get('/agal/my/requests', fn () => response()->json(['success' => true, 'data' => []]));
    Route::get('/agal/my/responses', fn () => response()->json(['success' => true, 'data' => []]));
});
