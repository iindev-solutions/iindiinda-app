<?php

namespace App\Http\Controllers\Tal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tal\Concerns\SerializesTalData;
use App\Models\TalMaster;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    use SerializesTalData;

    public function index(Request $request): JsonResponse
    {
        $query = TalMaster::query()
            ->with('master')
            ->where('status', 'open')
            ->latest();

        if ($request->filled('category')) {
            $query->where('category', (string) $request->string('category'));
        }

        if ($request->filled('availability_status')) {
            $query->where('availability_status', (string) $request->string('availability_status'));
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->string('location') . '%');
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()->map(fn (TalMaster $master) => $this->serializeMaster($master))->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');
        abort_if($user->role !== 'master', 403, 'Only masters can create TAL cards');

        $validated = $this->validateMasterPayload($request);

        $master = TalMaster::query()->create([
            'master_id' => $user->id,
            'category' => $validated['category'],
            'service_label' => $validated['service_label'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'availability_status' => $validated['availability_status'],
            'available_note' => $validated['available_note'] ?? null,
            'price_from' => $validated['price_from'] ?? null,
            'status' => 'open',
        ])->load('master');

        return response()->json([
            'success' => true,
            'data' => $this->serializeMaster($master),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $master = TalMaster::query()->with('master')->find($id);

        abort_if(!$master, 404, 'Master card not found');

        return response()->json([
            'success' => true,
            'data' => $this->serializeMaster($master),
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        $master = TalMaster::query()->with('master')->find($id);

        abort_if(!$master, 404, 'Master card not found');
        abort_if($master->master_id !== $user->id, 403, 'Forbidden');

        $validated = $this->validateMasterPayload($request, true);
        $nextStatus = $validated['status'] ?? null;

        if ($nextStatus !== null) {
            abort_if($nextStatus === 'matched', 422, 'Matched status is set by accepting a booking');
            abort_if($master->status === 'matched' && $nextStatus === 'open', 422, 'Use completed or cancelled after matching');
            abort_if(in_array($master->status, ['completed', 'cancelled'], true) && $nextStatus !== $master->status, 422, 'Master card is already final');
            abort_if($master->status === 'open' && $nextStatus === 'completed', 422, 'Master card must be matched before completion');
        }

        $master->fill($validated);
        $master->save();

        return response()->json([
            'success' => true,
            'data' => $this->serializeMaster($master->fresh()->load('master')),
        ]);
    }

    private function validateMasterPayload(Request $request, bool $partial = false): array
    {
        return $request->validate([
            'category' => ($partial ? 'sometimes|' : '') . 'required|in:beauty,home,repair',
            'service_label' => ($partial ? 'sometimes|' : '') . 'required|string|max:120',
            'description' => ($partial ? 'sometimes|' : '') . 'required|string|max:500',
            'location' => ($partial ? 'sometimes|' : '') . 'required|string|max:255',
            'availability_status' => ($partial ? 'sometimes|' : '') . 'required|in:now,later,tomorrow,busy',
            'available_note' => 'nullable|string|max:255',
            'price_from' => 'nullable|integer|min:0',
            'status' => 'sometimes|in:open,matched,completed,cancelled',
        ]);
    }
}
