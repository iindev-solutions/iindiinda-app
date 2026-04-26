<?php

namespace App\Http\Controllers\Agal\Concerns;

use App\Models\AgalRequest;
use App\Models\AgalResponse;
use App\Models\AgalRoute;
use App\Models\User;

trait SerializesAgalData
{
    protected function serializeRoute(AgalRoute $route): array
    {
        return [
            'id' => $route->id,
            'carrier' => $this->serializeUserSummary($route->carrier),
            'from_address' => $route->from_address,
            'to_address' => $route->to_address,
            'date' => $route->date,
            'time' => $route->time,
            'size_label' => $route->size_label,
            'weight_kg_max' => $route->weight_kg_max !== null ? (float) $route->weight_kg_max : null,
            'accepted_items' => $route->accepted_items,
            'restricted_items' => $route->restricted_items,
            'price' => $route->price,
            'notes' => $route->notes,
            'status' => $route->status,
            'created_at' => $route->created_at?->toIso8601String(),
        ];
    }

    protected function serializeRequest(AgalRequest $request): array
    {
        return [
            'id' => $request->id,
            'sender' => $this->serializeUserSummary($request->sender),
            'from_address' => $request->from_address,
            'to_address' => $request->to_address,
            'date' => $request->date,
            'time' => $request->time,
            'size_label' => $request->size_label,
            'weight_kg' => $request->weight_kg !== null ? (float) $request->weight_kg : null,
            'contents_summary' => $request->contents_summary,
            'fragility' => $request->fragility,
            'documents_required' => (bool) $request->documents_required,
            'budget' => $request->budget,
            'notes' => $request->notes,
            'status' => $request->status,
            'created_at' => $request->created_at?->toIso8601String(),
        ];
    }

    protected function serializeResponse(AgalResponse $response): array
    {
        return [
            'id' => $response->id,
            'route_id' => $response->route_id,
            'request_id' => $response->request_id,
            'route' => $response->relationLoaded('route') && $response->route ? $this->serializeRoute($response->route) : null,
            'request' => $response->relationLoaded('request') && $response->request ? $this->serializeRequest($response->request) : null,
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
