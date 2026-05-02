<?php

namespace App\Http\Controllers\Tal\Concerns;

use App\Models\TalBooking;
use App\Models\TalMaster;
use App\Models\User;

trait SerializesTalData
{
    protected function serializeMaster(TalMaster $master): array
    {
        return [
            'id' => $master->id,
            'master' => $this->serializeUserSummary($master->master),
            'category' => $master->category,
            'service_label' => $master->service_label,
            'description' => $master->description,
            'location' => $master->location,
            'availability_status' => $master->availability_status,
            'available_note' => $master->available_note,
            'price_from' => $master->price_from,
            'status' => $master->status,
            'created_at' => $master->created_at?->toIso8601String(),
        ];
    }

    protected function serializeBooking(TalBooking $booking): array
    {
        return [
            'id' => $booking->id,
            'tal_master_id' => $booking->tal_master_id,
            'tal_master' => $booking->relationLoaded('talMaster') && $booking->talMaster ? $this->serializeMaster($booking->talMaster) : null,
            'user' => $this->serializeUserSummary($booking->user),
            'message' => $booking->message,
            'desired_time' => $booking->desired_time,
            'status' => $booking->status,
            'created_at' => $booking->created_at?->toIso8601String(),
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
