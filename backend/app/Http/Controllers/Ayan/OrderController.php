<?php

namespace AppHttpControllersAyan;

use AppHttpControllersController;
use IlluminateHttpJsonResponse;
use IlluminateHttpRequest;

class OrderController extends Controller
{
    /**
     * Create new order (driver creates a ride offer)
     *
     * Body: { from_address: string, to_address: string, price: int (100-5000), date?: string, time?: string, seats?: int }
     * Reply: TaxiOrder
     */
    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'from_address' => 'required|string|max:255',
            'to_address' => 'required|string|max:255',
            'price' => 'required|integer|min:100|max:5000',
            'date' => 'nullable|date',
            'time' => 'nullable|string',
            'seats' => 'nullable|integer|min:1|max:8',
        ]);

        $order = [
            'id' => rand(1, 10000),
            'from_address' => $request->input('from_address'),
            'to_address' => $request->input('to_address'),
            'price' => $request->input('price'),
            'date' => $request->input('date', date('Y-m-d')),
            'time' => $request->input('time', date('H:i')),
            'seats' => $request->input('seats', 1),
            'status' => 'open',
            'driver_id' => 1,
            'passenger_id' => null,
            'created_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
        ];

        return response()->json($order);
    }

    /**
     * Get open orders (available rides)
     *
     * Reply: TaxiOrder[] (status = open, ordered by created_at desc)
     */
    public function openOrders(): JsonResponse
    {
        // Mock data for MVP
        $orders = [
            [
                'id' => 1,
                'from_address' => 'Марха',
                'to_address' => 'Порт',
                'price' => 200,
                'date' => date('Y-m-d'),
                'time' => '06:00',
                'seats' => 2,
                'status' => 'open',
                'driver_id' => 1,
                'passenger_id' => null,
                'created_at' => now()->subHours(2)->toIso8601String(),
                'updated_at' => now()->subHours(2)->toIso8601String(),
                'driver' => [
                    'id' => 1,
                    'first_name' => 'Вася',
                    'username' => 'vasya_driver',
                    'rating' => 4.8,
                ],
            ],
            [
                'id' => 2,
                'from_address' => 'Якутск',
                'to_address' => 'Намцы',
                'price' => 350,
                'date' => date('Y-m-d'),
                'time' => '10:00',
                'seats' => 3,
                'status' => 'open',
                'driver_id' => 2,
                'passenger_id' => null,
                'created_at' => now()->subHours(5)->toIso8601String(),
                'updated_at' => now()->subHours(5)->toIso8601String(),
                'driver' => [
                    'id' => 2,
                    'first_name' => 'Петр',
                    'username' => 'petya_driver',
                    'rating' => 4.5,
                ],
            ],
        ];

        return response()->json($orders);
    }

    /**
     * Get my active order
     *
     * Reply: TaxiOrder | null
     */
    public function myOrder(): JsonResponse
    {
        // TODO: Check auth token, return user's active order
        return response()->json(null);
    }

    /**
     * Accept an order (driver accepts passenger)
     *
     * Rules: только driver, заказ должен быть open, у водителя нет активного
     * Reply: TaxiOrder (status = accepted)
     */
    public function accept(int $id): JsonResponse
    {
        $order = [
            'id' => $id,
            'from_address' => 'Марха',
            'to_address' => 'Порт',
            'price' => 200,
            'date' => date('Y-m-d'),
            'time' => '06:00',
            'seats' => 2,
            'status' => 'accepted',
            'driver_id' => 1,
            'passenger_id' => 3,
            'created_at' => now()->subHours(2)->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
        ];

        return response()->json($order);
    }

    /**
     * Complete an order
     *
     * Rules: только участники заказа (passenger или driver)
     * Reply: TaxiOrder (status = completed)
     */
    public function complete(int $id): JsonResponse
    {
        $order = [
            'id' => $id,
            'from_address' => 'Марха',
            'to_address' => 'Порт',
            'price' => 200,
            'date' => date('Y-m-d'),
            'time' => '06:00',
            'seats' => 2,
            'status' => 'completed',
            'driver_id' => 1,
            'passenger_id' => 3,
            'created_at' => now()->subHours(3)->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
        ];

        return response()->json($order);
    }

    /**
     * Cancel an order
     *
     * Rules: только passenger, заказ open или accepted
     * Reply: TaxiOrder (status = cancelled)
     */
    public function cancel(int $id): JsonResponse
    {
        $order = [
            'id' => $id,
            'from_address' => 'Марха',
            'to_address' => 'Порт',
            'price' => 200,
            'date' => date('Y-m-d'),
            'time' => '06:00',
            'seats' => 2,
            'status' => 'cancelled',
            'driver_id' => 1,
            'passenger_id' => null,
            'created_at' => now()->subHours(1)->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
        ];

        return response()->json($order);
    }
}