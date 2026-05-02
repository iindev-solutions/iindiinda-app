<?php

namespace App\Http\Controllers\Agal;

use App\Http\Controllers\Agal\Concerns\SerializesAgalData;
use App\Http\Controllers\Controller;
use App\Models\AgalRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    use SerializesAgalData;

    public function index(Request $request): JsonResponse
    {
        $query = AgalRequest::query()
            ->with('sender')
            ->where('status', 'open')
            ->upcomingForFeed()
            ->latest();

        if ($request->filled('from')) {
            $query->where('from_address', 'like', '%' . $request->string('from') . '%');
        }

        if ($request->filled('to')) {
            $query->where('to_address', 'like', '%' . $request->string('to') . '%');
        }

        if ($request->filled('date')) {
            $query->whereDate('date', (string) $request->string('date'));
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()->map(fn (AgalRequest $item) => $this->serializeRequest($item))->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');
        abort_if($user->role !== 'sender', 403, 'Only senders can create requests');

        $validated = $request->validate([
            'from_address' => 'required|string|max:255',
            'to_address' => 'required|string|max:255',
            'date' => 'required|date|after_or_equal:today',
            'time' => ['nullable', 'regex:/^([01]\d|2[0-3]):([0-5]\d)$/'],
            'size_label' => 'required|in:document,small,medium,large',
            'weight_kg' => 'nullable|numeric|gt:0|max:999999.99',
            'contents_summary' => 'required|string|max:255',
            'fragility' => 'required|in:normal,fragile',
            'documents_required' => 'required|boolean',
            'budget' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $item = AgalRequest::query()->create([
            'sender_id' => $user->id,
            'from_address' => $validated['from_address'],
            'to_address' => $validated['to_address'],
            'date' => $validated['date'],
            'time' => $validated['time'] ?? null,
            'size_label' => $validated['size_label'],
            'weight_kg' => $validated['weight_kg'] ?? null,
            'contents_summary' => $validated['contents_summary'],
            'fragility' => $validated['fragility'],
            'documents_required' => $validated['documents_required'],
            'budget' => $validated['budget'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'open',
        ])->load('sender');

        return response()->json([
            'success' => true,
            'data' => $this->serializeRequest($item),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $item = AgalRequest::query()->with('sender')->find($id);

        abort_if(!$item, 404, 'Request not found');

        return response()->json([
            'success' => true,
            'data' => $this->serializeRequest($item),
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user instanceof User, 401, 'Unauthenticated.');

        $validated = $request->validate([
            'status' => 'sometimes|in:open,matched,completed,cancelled',
            'time' => ['nullable', 'regex:/^([01]\d|2[0-3]):([0-5]\d)$/'],
            'weight_kg' => 'nullable|numeric|gt:0|max:999999.99',
            'contents_summary' => 'sometimes|string|max:255',
            'fragility' => 'sometimes|in:normal,fragile',
            'documents_required' => 'sometimes|boolean',
            'budget' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $item = AgalRequest::query()->with('sender')->find($id);

        abort_if(!$item, 404, 'Request not found');
        abort_if($item->sender_id !== $user->id, 403, 'Forbidden');

        if (array_key_exists('status', $validated)) {
            $nextStatus = $validated['status'];

            abort_if($nextStatus === 'matched', 422, 'Matched status is set by accepting a response');
            abort_if($item->status === 'matched' && $nextStatus === 'open', 422, 'Use completed or cancelled after matching');
            abort_if(in_array($item->status, ['completed', 'cancelled'], true) && $nextStatus !== $item->status, 422, 'Request is already final');
            abort_if($item->status === 'open' && $nextStatus === 'completed', 422, 'Request must be matched before completion');
        }

        $item->fill($validated);
        $item->save();

        return response()->json([
            'success' => true,
            'data' => $this->serializeRequest($item->fresh()->load('sender')),
        ]);
    }
}
