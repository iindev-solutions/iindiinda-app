<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| iind API Routes
|--------------------------------------------------------------------------
|
| Все роуты для iind ecosystem.
| Формат: /api/{service}/{resource}
|
*/

// ==========================================
// Health Check
// ==========================================
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'version' => '0.1.0']);
});

// ==========================================
// AUTH - Telegram авторизация
// ==========================================
Route::post('/auth/telegram', 'AuthController@loginViaTelegram');
// Body:  { "init_data": "string (Telegram WebApp initData)" }
// Reply: { "token": "string", "user": User }

// ==========================================
// USER - Текущий пользователь
// ==========================================
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', 'UserController@me');
    // Reply: User { id, telegram_id, username, first_name, role, created_at }

    Route::post('/user/switch-role', 'UserController@switchRole');
    // Body:  { "role": "passenger" | "driver" }
    // Reply: { "user": User }

    // ==========================================
    // AYAN (Бардыбыт / Такси)
    // ==========================================

    Route::post('/ayan/orders', 'Ayan\OrderController@create');
    // Body:  { "from_address": "string", "to_address": "string", "price": int (100-5000) }
    // Reply: TaxiOrder
    // Rules: 400 если есть активный заказ. price min:100 max:5000

    Route::get('/ayan/orders/open', 'Ayan\OrderController@openOrders');
    // Reply: TaxiOrder[] (status = open, ordered by created_at desc)

    Route::get('/ayan/orders/my', 'Ayan\OrderController@myOrder');
    // Reply: TaxiOrder | null (активный заказ текущего юзера)

    Route::post('/ayan/orders/{id}/accept', 'Ayan\OrderController@accept');
    // Rules: только driver, заказ должен быть open, у водителя нет активного
    // Reply: TaxiOrder (status = accepted)

    Route::post('/ayan/orders/{id}/complete', 'Ayan\OrderController@complete');
    // Rules: только участники заказа (passenger или driver)
    // Reply: TaxiOrder (status = completed)

    Route::post('/ayan/orders/{id}/cancel', 'Ayan\OrderController@cancel');
    // Rules: только passenger, заказ open или accepted
    // Reply: TaxiOrder (status = cancelled)

    // ==========================================
    // TAL (Бронирование)
    // ==========================================

    Route::get('/tal/services', 'Tal\ServiceController@index');
    // Reply: Service[] { id, name, duration, price }

    Route::get('/tal/masters', 'Tal\MasterController@index');
    // Query: ?service_id=string
    // Reply: Master[] { id, name, specialization, rating, review_count }

    Route::get('/tal/slots', 'Tal\SlotController@available');
    // Query: ?master_id=string&date=YYYY-MM-DD
    // Reply: TimeSlot[] { id, time, available, date }

    Route::post('/tal/bookings', 'Tal\BookingController@create');
    // Body:  { "service_id": "string", "master_id": "string", "time_slot_id": "string", "date": "string" }
    // Reply: Booking { id, service_id, master_id, time_slot_id, date, time, status }

    Route::delete('/tal/bookings/{id}', 'Tal\BookingController@cancel');
    // Reply: 204 No Content

    // ==========================================
    // UUS (Мастера)
    // ==========================================

    Route::post('/uus/tasks', 'Uus\TaskController@create');
    // Body:  { "title": "string", "description": "string", "category": "string", "budget": int | null }
    // Reply: Task

    Route::get('/uus/tasks/open', 'Uus\TaskController@openTasks');
    // Reply: Task[] (status = open)

    Route::post('/uus/tasks/{id}/respond', 'Uus\TaskController@respond');
    // Body:  { "message": "string", "price": int }
    // Reply: Response

    // ==========================================
    // AGAL (Доставка)
    // ==========================================

    Route::post('/agal/parcels', 'Agal\ParcelController@create');
    // Body:  { "from_city": "string", "to_city": "string", "weight_kg": float, "description": "string" }
    // Reply: Parcel

    Route::get('/agal/parcels/open', 'Agal\ParcelController@openParcels');
    // Reply: Parcel[] (status = looking_for_carrier)

    Route::post('/agal/parcels/{id}/take', 'Agal\ParcelController@take');
    // Rules: только carrier
    // Reply: Parcel (status = in_transit)

});
