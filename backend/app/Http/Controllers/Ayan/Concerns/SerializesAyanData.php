<?php

namespace App\Http\Controllers\Ayan\Concerns;

use App\Models\AyanRequest;
use App\Models\AyanResponse;
use App\Models\Trip;
use App\Models\User;

trait SerializesAyanData
{
    protected function serializeTrip(Trip $trip): array
    {
        return [
            'id' => $trip->id,
            'driver' => $this->serializeUserSummary($trip->driver),
            'from_address' => $trip->from_address,
            'to_address' => $trip->to_address,
            'date' => $trip->date,
            'time' => $trip->time,
            'seats' => $trip->seats,
            'price' => $trip->price,
            'comment' => $trip->comment,
            'status' => $trip->status,
            'created_at' => $trip->created_at?->toIso8601String(),
        ];
    }

    protected function serializeRequest(AyanRequest $request): array
    {
        return [
            'id' => $request->id,
            'passenger' => $this->serializeUserSummary($request->passenger),
            'from_address' => $request->from_address,
            'to_address' => $request->to_address,
            'date' => $request->date,
            'time' => $request->time,
            'description' => $request->description,
            'status' => $request->status,
            'created_at' => $request->created_at?->toIso8601String(),
        ];
    }

    protected function serializeResponse(AyanResponse $response): array
    {
        return [
            'id' => $response->id,
            'trip_id' => $response->trip_id,
            'request_id' => $response->request_id,
            'user' => $this->serializeUserSummary($response->user),
            'message' => $response->message,
            'status' => $response->status,
            'created_at' => $response->created_at?->toIso8601String(),
        ];
    }

    protected function serializeUserSummary(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->first_name,
            'username' => $user->username,
        ];
    }
}
