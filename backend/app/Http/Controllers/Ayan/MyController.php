<?php

namespace App\Http\Controllers\Ayan;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class MyController extends Controller
{
    public function trips(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                [
                    'id' => 101,
                    'driver' => [
                        'id' => 1,
                        'name' => 'Тест Driver',
                        'username' => 'test_driver',
                    ],
                    'from_address' => 'Якутск',
                    'to_address' => 'Хатассы',
                    'date' => now()->toDateString(),
                    'time' => '08:00',
                    'seats' => 3,
                    'price' => 300,
                    'comment' => null,
                    'status' => 'open',
                    'created_at' => now()->subHour()->toIso8601String(),
                ],
            ],
        ]);
    }

    public function requests(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                [
                    'id' => 201,
                    'passenger' => [
                        'id' => 1,
                        'name' => 'Тест Passenger',
                        'username' => 'test_passenger',
                    ],
                    'from_address' => 'Тулагино',
                    'to_address' => 'Якутск',
                    'date' => now()->addDay()->toDateString(),
                    'time' => null,
                    'description' => 'После обеда',
                    'status' => 'open',
                    'created_at' => now()->subMinutes(20)->toIso8601String(),
                ],
            ],
        ]);
    }

    public function responses(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                [
                    'id' => 301,
                    'trip_id' => 101,
                    'request_id' => null,
                    'user' => [
                        'id' => 1,
                        'name' => 'Тест User',
                        'username' => 'test_user',
                    ],
                    'message' => 'Подходит',
                    'status' => 'pending',
                    'created_at' => now()->subMinutes(10)->toIso8601String(),
                ],
            ],
        ]);
    }
}
