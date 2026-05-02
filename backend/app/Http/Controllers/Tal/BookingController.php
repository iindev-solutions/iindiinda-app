<?php

namespace App\Http\Controllers\Tal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tal\Concerns\SerializesTalData;
use App\Models\TalBooking;
use App\Models\TalMaster;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    use SerializesTalData;

    public function masterIndex(int $masterId): JsonResponse
    {
        /** @var User|null $user */
        $user = request()->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        $master = TalMaster::query()->find($masterId);

        abort_if(!$master, 404, 'Master card not found');
        abort_if($master->master_id !== $user->id, 403, 'Forbidden');

        return response()->json([
            'success' => true,
            'data' => TalBooking::query()
                ->with('user')
                ->where('tal_master_id', $masterId)
                ->latest()
                ->get()
                ->map(fn (TalBooking $booking) => $this->serializeBooking($booking))
                ->values(),
        ]);
    }

    public function masterStore(Request $request, int $masterId): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        $validated = $request->validate([
            'message' => 'nullable|string|max:500',
            'desired_time' => 'nullable|string|max:255',
        ]);

        if (($validated['message'] ?? null) === null && ($validated['desired_time'] ?? null) === null) {
            throw ValidationException::withMessages([
                'message' => ['Message or desired time is required'],
            ]);
        }

        $master = TalMaster::query()->with('master')->find($masterId);

        abort_if(!$master, 404, 'Master card not found');
        abort_if($master->status !== 'open', 422, 'Master card is closed');
        abort_if($master->availability_status === 'busy', 422, 'Master is busy');
        abort_if($master->master_id === $user->id, 422, 'Cannot book your own card');
        abort_if(
            TalBooking::query()->where('tal_master_id', $master->id)->where('user_id', $user->id)->exists(),
            422,
            'You already booked this master'
        );

        $booking = TalBooking::query()->create([
            'tal_master_id' => $master->id,
            'user_id' => $user->id,
            'message' => $validated['message'] ?? null,
            'desired_time' => $validated['desired_time'] ?? null,
            'status' => 'pending',
        ])->load('user');

        return response()->json([
            'success' => true,
            'data' => $this->serializeBooking($booking),
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

        $booking = TalBooking::query()->with(['user', 'talMaster.master'])->find($id);

        abort_if(!$booking, 404, 'Booking not found');
        abort_if($booking->talMaster?->master_id !== $user->id, 403, 'Forbidden');
        abort_if($booking->status !== 'pending', 422, 'Booking is not pending');
        abort_if($booking->talMaster && $booking->talMaster->status !== 'open', 422, 'Master card is not open');

        if ($validated['status'] === 'accepted') {
            $conflictQuery = TalBooking::query()
                ->where('tal_master_id', $booking->tal_master_id)
                ->whereKeyNot($booking->id)
                ->where('status', 'accepted');

            abort_if($conflictQuery->exists(), 422, 'Another booking was already accepted');
        }

        DB::transaction(function () use ($booking, $validated) {
            $booking->status = $validated['status'];
            $booking->save();

            if ($validated['status'] !== 'accepted') {
                return;
            }

            TalMaster::query()->whereKey($booking->tal_master_id)->update(['status' => 'matched']);
            TalBooking::query()
                ->where('tal_master_id', $booking->tal_master_id)
                ->whereKeyNot($booking->id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);
        });

        return response()->json([
            'success' => true,
            'data' => $this->serializeBooking($booking->fresh()->load('user')),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        $booking = TalBooking::query()->find($id);

        abort_if(!$booking, 404, 'Booking not found');
        abort_if($booking->user_id !== $user->id, 403, 'Forbidden');
        abort_if($booking->status !== 'pending', 422, 'Only pending bookings can be deleted');

        $booking->delete();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $id,
                'deleted' => true,
            ],
        ]);
    }
}
