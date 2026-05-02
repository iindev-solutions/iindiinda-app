<?php

namespace App\Http\Controllers\Tal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tal\Concerns\SerializesTalData;
use App\Models\TalBooking;
use App\Models\TalMaster;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MyController extends Controller
{
    use SerializesTalData;

    public function masters(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        return response()->json([
            'success' => true,
            'data' => TalMaster::query()
                ->with('master')
                ->where('master_id', $user->id)
                ->latest()
                ->get()
                ->map(fn (TalMaster $master) => $this->serializeMaster($master))
                ->values(),
        ]);
    }

    public function bookings(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        return response()->json([
            'success' => true,
            'data' => TalBooking::query()
                ->with(['user', 'talMaster.master'])
                ->where('user_id', $user->id)
                ->latest()
                ->get()
                ->map(fn (TalBooking $booking) => $this->serializeBooking($booking))
                ->values(),
        ]);
    }
}
