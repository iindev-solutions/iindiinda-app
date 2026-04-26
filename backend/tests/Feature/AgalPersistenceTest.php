<?php

namespace Tests\Feature;

use App\Models\AgalRequest;
use App\Models\AgalResponse;
use App\Models\AgalRoute;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AgalPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_route_endpoints_use_persisted_data(): void
    {
        $owner = $this->makeUser('owner_route', 9101, 'carrier');
        $other = $this->makeUser('other_route', 9102, 'carrier');
        $routeDate = now()->addDay()->toDateString();

        AgalRoute::create([
            'carrier_id' => $other->id,
            'from_address' => 'Moscow',
            'to_address' => 'Yakutsk',
            'date' => now()->addDays(2)->toDateString(),
            'time' => '11:00',
            'size_label' => 'small',
            'weight_kg_max' => 3,
            'accepted_items' => 'documents',
            'restricted_items' => 'fragile',
            'price' => 800,
            'notes' => 'other route',
            'status' => 'open',
        ]);

        Sanctum::actingAs($owner);

        $created = $this->postJson('/api/agal/routes', [
            'from_address' => 'Yakutsk',
            'to_address' => 'Novosibirsk',
            'date' => $routeDate,
            'time' => '09:30',
            'size_label' => 'document',
            'weight_kg_max' => 1.5,
            'accepted_items' => 'documents',
            'restricted_items' => 'liquids',
            'price' => 500,
            'notes' => 'persisted route',
        ])->assertCreated();

        $routeId = $created->json('data.id');

        $this->getJson("/api/agal/routes?from=Yakut&date={$routeDate}")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $routeId)
            ->assertJsonPath('data.0.carrier.id', $owner->id)
            ->assertJsonPath('data.0.carrier.name', $owner->first_name);

        $this->getJson("/api/agal/routes/{$routeId}")
            ->assertOk()
            ->assertJsonPath('data.notes', 'persisted route');

        $this->patchJson("/api/agal/routes/{$routeId}", [
            'status' => 'cancelled',
            'price' => 700,
        ])->assertOk()
            ->assertJsonPath('data.status', 'cancelled')
            ->assertJsonPath('data.price', 700);

        $this->getJson('/api/agal/my/routes')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $routeId);
    }

    public function test_request_endpoints_use_persisted_data(): void
    {
        $owner = $this->makeUser('owner_request', 9201, 'sender');
        $other = $this->makeUser('other_request', 9202, 'sender');
        $requestDate = now()->addDay()->toDateString();

        AgalRequest::create([
            'sender_id' => $other->id,
            'from_address' => 'Yakutsk',
            'to_address' => 'Moscow',
            'date' => now()->addDays(3)->toDateString(),
            'time' => '12:00',
            'size_label' => 'small',
            'weight_kg' => 2,
            'contents_summary' => 'other request',
            'fragility' => 'normal',
            'documents_required' => false,
            'budget' => 1200,
            'notes' => 'other',
            'status' => 'open',
        ]);

        Sanctum::actingAs($owner);

        $created = $this->postJson('/api/agal/requests', [
            'from_address' => 'Yakutsk',
            'to_address' => 'Novosibirsk',
            'date' => $requestDate,
            'time' => '17:15',
            'size_label' => 'document',
            'weight_kg' => 0.2,
            'contents_summary' => 'A4 documents',
            'fragility' => 'fragile',
            'documents_required' => true,
            'budget' => 1000,
            'notes' => 'persisted request',
        ])->assertCreated();

        $requestId = $created->json('data.id');

        $this->getJson("/api/agal/requests?to=Novo&date={$requestDate}")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $requestId)
            ->assertJsonPath('data.0.sender.id', $owner->id)
            ->assertJsonPath('data.0.sender.name', $owner->first_name);

        $this->getJson("/api/agal/requests/{$requestId}")
            ->assertOk()
            ->assertJsonPath('data.contents_summary', 'A4 documents');

        $this->patchJson("/api/agal/requests/{$requestId}", [
            'status' => 'cancelled',
            'budget' => 1500,
        ])->assertOk()
            ->assertJsonPath('data.status', 'cancelled')
            ->assertJsonPath('data.budget', 1500);

        $this->getJson('/api/agal/my/requests')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $requestId);
    }

    public function test_response_endpoints_use_persisted_data_and_final_lifecycle(): void
    {
        $routeOwner = $this->makeUser('route_owner', 9301, 'carrier');
        $routeResponder = $this->makeUser('route_responder', 9302, 'sender');
        $requestOwner = $this->makeUser('request_owner', 9303, 'sender');
        $requestResponder = $this->makeUser('request_responder', 9304, 'carrier');

        $route = AgalRoute::create([
            'carrier_id' => $routeOwner->id,
            'from_address' => 'Yakutsk',
            'to_address' => 'Moscow',
            'date' => now()->addDay()->toDateString(),
            'time' => '08:00',
            'size_label' => 'small',
            'weight_kg_max' => 2,
            'accepted_items' => 'documents',
            'restricted_items' => 'fragile',
            'price' => 900,
            'notes' => null,
            'status' => 'open',
        ]);

        $request = AgalRequest::create([
            'sender_id' => $requestOwner->id,
            'from_address' => 'Yakutsk',
            'to_address' => 'Novosibirsk',
            'date' => now()->addDays(2)->toDateString(),
            'time' => '16:00',
            'size_label' => 'document',
            'weight_kg' => 0.3,
            'contents_summary' => 'docs',
            'fragility' => 'normal',
            'documents_required' => true,
            'budget' => 1500,
            'notes' => null,
            'status' => 'open',
        ]);

        Sanctum::actingAs($routeResponder);
        $routeResponse = $this->postJson("/api/agal/routes/{$route->id}/responses", [
            'message' => 'can hand over today',
        ])->assertCreated();

        Sanctum::actingAs($requestResponder);
        $requestResponse = $this->postJson("/api/agal/requests/{$request->id}/responses", [
            'message' => 'I can take it',
        ])->assertCreated();

        $routeResponseId = $routeResponse->json('data.id');
        $requestResponseId = $requestResponse->json('data.id');

        Sanctum::actingAs($routeOwner);
        $this->getJson("/api/agal/routes/{$route->id}/responses")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $routeResponseId);

        $this->patchJson("/api/agal/responses/{$routeResponseId}", [
            'status' => 'accepted',
        ])->assertOk()
            ->assertJsonPath('data.status', 'accepted');

        $this->patchJson("/api/agal/routes/{$route->id}", [
            'status' => 'completed',
        ])->assertOk()
            ->assertJsonPath('data.status', 'completed');

        Sanctum::actingAs($requestOwner);
        $this->getJson("/api/agal/requests/{$request->id}/responses")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $requestResponseId);

        $this->patchJson("/api/agal/responses/{$requestResponseId}", [
            'status' => 'accepted',
        ])->assertOk()
            ->assertJsonPath('data.status', 'accepted');

        $this->patchJson("/api/agal/requests/{$request->id}", [
            'status' => 'cancelled',
        ])->assertOk()
            ->assertJsonPath('data.status', 'cancelled');

        Sanctum::actingAs($routeResponder);
        $this->getJson('/api/agal/my/responses')
            ->assertOk()
            ->assertJsonPath('data.0.id', $routeResponseId)
            ->assertJsonPath('data.0.route.id', $route->id)
            ->assertJsonPath('data.0.route.status', 'completed');

        $this->deleteJson("/api/agal/responses/{$routeResponseId}")
            ->assertStatus(422);

        Sanctum::actingAs($requestResponder);
        $this->getJson('/api/agal/my/responses')
            ->assertOk()
            ->assertJsonPath('data.0.id', $requestResponseId)
            ->assertJsonPath('data.0.request.id', $request->id)
            ->assertJsonPath('data.0.request.status', 'cancelled');

        $this->deleteJson("/api/agal/responses/{$requestResponseId}")
            ->assertStatus(422);

        $this->assertDatabaseHas('agal_responses', [
            'id' => $routeResponseId,
            'status' => 'accepted',
        ]);
        $this->assertDatabaseHas('agal_responses', [
            'id' => $requestResponseId,
            'status' => 'accepted',
        ]);
    }

    public function test_role_rules_apply_to_agal_creation_and_responses(): void
    {
        $sender = $this->makeUser('sender_role', 9401, 'sender');
        $carrier = $this->makeUser('carrier_role', 9402, 'carrier');
        $routeOwner = $this->makeUser('route_owner_role', 9403, 'carrier');
        $requestOwner = $this->makeUser('request_owner_role', 9404, 'sender');

        $route = AgalRoute::create([
            'carrier_id' => $routeOwner->id,
            'from_address' => 'Yakutsk',
            'to_address' => 'Moscow',
            'date' => now()->addDay()->toDateString(),
            'time' => '09:00',
            'size_label' => 'small',
            'weight_kg_max' => 2,
            'accepted_items' => 'documents',
            'restricted_items' => 'fragile',
            'price' => 600,
            'notes' => null,
            'status' => 'open',
        ]);

        $request = AgalRequest::create([
            'sender_id' => $requestOwner->id,
            'from_address' => 'Yakutsk',
            'to_address' => 'Novosibirsk',
            'date' => now()->addDays(2)->toDateString(),
            'time' => '12:00',
            'size_label' => 'document',
            'weight_kg' => 0.5,
            'contents_summary' => 'docs',
            'fragility' => 'normal',
            'documents_required' => false,
            'budget' => 800,
            'notes' => null,
            'status' => 'open',
        ]);

        Sanctum::actingAs($sender);
        $this->postJson('/api/agal/routes', [
            'from_address' => 'Yakutsk',
            'to_address' => 'Moscow',
            'date' => now()->addDay()->toDateString(),
            'size_label' => 'small',
        ])->assertForbidden();

        Sanctum::actingAs($carrier);
        $this->postJson('/api/agal/requests', [
            'from_address' => 'Yakutsk',
            'to_address' => 'Novosibirsk',
            'date' => now()->addDay()->toDateString(),
            'size_label' => 'document',
            'contents_summary' => 'docs',
            'fragility' => 'normal',
            'documents_required' => false,
        ])->assertForbidden();

        Sanctum::actingAs($carrier);
        $this->postJson("/api/agal/routes/{$route->id}/responses", [
            'message' => 'wrong role',
        ])->assertForbidden();

        Sanctum::actingAs($sender);
        $this->postJson("/api/agal/requests/{$request->id}/responses", [
            'message' => 'wrong role',
        ])->assertForbidden();
    }

    private function makeUser(string $name, int $telegramId, string $role): User
    {
        return User::create([
            'telegram_id' => $telegramId,
            'first_name' => $name,
            'username' => $name,
            'role' => $role,
            'rating' => 5.0,
            'completed_orders' => 0,
            'is_available' => true,
        ]);
    }
}
