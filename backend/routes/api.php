<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Ayan\MyController;
use App\Http\Controllers\Ayan\RequestController;
use App\Http\Controllers\Ayan\ResponseController;
use App\Http\Controllers\Ayan\TripController;
use App\Http\Controllers\UserController;
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

    Route::post('/agal/parcels', fn () => response()->json([], 201));
    Route::get('/agal/parcels/open', fn () => response()->json([]));
    Route::post('/agal/parcels/{id}/take', fn () => response()->json([], 201));
});
