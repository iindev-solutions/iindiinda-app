<?php

use App\Http\Controllers\Agal\MyController as AgalMyController;
use App\Http\Controllers\Agal\RequestController as AgalRequestController;
use App\Http\Controllers\Agal\ResponseController as AgalResponseController;
use App\Http\Controllers\Agal\RouteController as AgalRouteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Ayan\MyController as AyanMyController;
use App\Http\Controllers\Ayan\RequestController as AyanRequestController;
use App\Http\Controllers\Ayan\ResponseController as AyanResponseController;
use App\Http\Controllers\Ayan\TripController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Tal\BookingController as TalBookingController;
use App\Http\Controllers\Tal\MasterController as TalMasterController;
use App\Http\Controllers\Tal\MyController as TalMyController;
use App\Http\Controllers\Uus\MyController as UusMyController;
use App\Http\Controllers\Uus\ResponseController as UusResponseController;
use App\Http\Controllers\Uus\TaskController as UusTaskController;
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

    Route::get('/ayan/requests', [AyanRequestController::class, 'index']);
    Route::post('/ayan/requests', [AyanRequestController::class, 'store']);
    Route::get('/ayan/requests/{id}', [AyanRequestController::class, 'show']);
    Route::patch('/ayan/requests/{id}', [AyanRequestController::class, 'update']);

    Route::get('/ayan/trips/{id}/responses', [AyanResponseController::class, 'tripIndex']);
    Route::get('/ayan/requests/{id}/responses', [AyanResponseController::class, 'requestIndex']);
    Route::post('/ayan/trips/{id}/responses', [AyanResponseController::class, 'tripStore']);
    Route::post('/ayan/requests/{id}/responses', [AyanResponseController::class, 'requestStore']);
    Route::patch('/ayan/responses/{id}', [AyanResponseController::class, 'update']);
    Route::delete('/ayan/responses/{id}', [AyanResponseController::class, 'destroy']);

    Route::get('/ayan/my/trips', [AyanMyController::class, 'trips']);
    Route::get('/ayan/my/requests', [AyanMyController::class, 'requests']);
    Route::get('/ayan/my/responses', [AyanMyController::class, 'responses']);

    Route::get('/tal/masters', [TalMasterController::class, 'index']);
    Route::post('/tal/masters', [TalMasterController::class, 'store']);
    Route::get('/tal/masters/{id}', [TalMasterController::class, 'show']);
    Route::patch('/tal/masters/{id}', [TalMasterController::class, 'update']);
    Route::get('/tal/masters/{id}/bookings', [TalBookingController::class, 'masterIndex']);
    Route::post('/tal/masters/{id}/bookings', [TalBookingController::class, 'masterStore']);
    Route::patch('/tal/bookings/{id}', [TalBookingController::class, 'update']);
    Route::delete('/tal/bookings/{id}', [TalBookingController::class, 'destroy']);
    Route::get('/tal/my/masters', [TalMyController::class, 'masters']);
    Route::get('/tal/my/bookings', [TalMyController::class, 'bookings']);

    Route::get('/uus/tasks', [UusTaskController::class, 'index']);
    Route::post('/uus/tasks', [UusTaskController::class, 'store']);
    Route::get('/uus/tasks/{id}', [UusTaskController::class, 'show']);
    Route::patch('/uus/tasks/{id}', [UusTaskController::class, 'update']);
    Route::get('/uus/tasks/{id}/responses', [UusResponseController::class, 'taskIndex']);
    Route::post('/uus/tasks/{id}/responses', [UusResponseController::class, 'taskStore']);
    Route::patch('/uus/responses/{id}', [UusResponseController::class, 'update']);
    Route::delete('/uus/responses/{id}', [UusResponseController::class, 'destroy']);
    Route::get('/uus/my/tasks', [UusMyController::class, 'tasks']);
    Route::get('/uus/my/responses', [UusMyController::class, 'responses']);

    Route::get('/agal/routes', [AgalRouteController::class, 'index']);
    Route::post('/agal/routes', [AgalRouteController::class, 'store']);
    Route::get('/agal/routes/{id}', [AgalRouteController::class, 'show']);
    Route::patch('/agal/routes/{id}', [AgalRouteController::class, 'update']);

    Route::get('/agal/requests', [AgalRequestController::class, 'index']);
    Route::post('/agal/requests', [AgalRequestController::class, 'store']);
    Route::get('/agal/requests/{id}', [AgalRequestController::class, 'show']);
    Route::patch('/agal/requests/{id}', [AgalRequestController::class, 'update']);

    Route::get('/agal/routes/{id}/responses', [AgalResponseController::class, 'routeIndex']);
    Route::post('/agal/routes/{id}/responses', [AgalResponseController::class, 'routeStore']);
    Route::get('/agal/requests/{id}/responses', [AgalResponseController::class, 'requestIndex']);
    Route::post('/agal/requests/{id}/responses', [AgalResponseController::class, 'requestStore']);
    Route::patch('/agal/responses/{id}', [AgalResponseController::class, 'update']);
    Route::delete('/agal/responses/{id}', [AgalResponseController::class, 'destroy']);

    Route::get('/agal/my/routes', [AgalMyController::class, 'routes']);
    Route::get('/agal/my/requests', [AgalMyController::class, 'requests']);
    Route::get('/agal/my/responses', [AgalMyController::class, 'responses']);
});
