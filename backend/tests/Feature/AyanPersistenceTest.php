<?php

namespace Tests\Feature;

use App\Models\AyanRequest;
use App\Models\AyanResponse;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AyanPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_trip_endpoints_use_persisted_data(): void
    {
        $owner = $this->makeUser('owner_trip', 1001, 'driver');
        $other = $this->makeUser('other_trip', 1002, 'driver');

        Trip::create([
            'driver_id' => $other->id,
            'from_address' => 'Намцы',
            'to_address' => 'Якутск',
            'date' => '2026-04-25',
            'time' => '08:00',
            'seats' => 2,
            'price' => 600,
            'comment' => 'other',
            'status' => 'open',
        ]);

        Sanctum::actingAs($owner);

        $created = $this->postJson('/api/ayan/trips', [
            'from_address' => 'Якутск',
            'to_address' => 'Намцы',
            'date' => '2026-04-24',
            'time' => '09:15',
            'seats' => 3,
            'price' => 500,
            'comment' => 'persisted trip',
        ])->assertCreated();

        $tripId = $created->json('data.id');

        $this->getJson('/api/ayan/trips?from=Якут&date=2026-04-24')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $tripId)
            ->assertJsonPath('data.0.driver.id', $owner->id)
            ->assertJsonPath('data.0.driver.name', $owner->first_name);

        $this->getJson("/api/ayan/trips/{$tripId}")
            ->assertOk()
            ->assertJsonPath('data.comment', 'persisted trip');

        $this->patchJson("/api/ayan/trips/{$tripId}", [
            'status' => 'closed',
            'price' => 700,
        ])->assertOk()
            ->assertJsonPath('data.status', 'closed')
            ->assertJsonPath('data.price', 700);

        $this->getJson('/api/ayan/my/trips')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $tripId);
    }

    public function test_request_endpoints_use_persisted_data(): void
    {
        $owner = $this->makeUser('owner_request', 2001, 'passenger');
        $other = $this->makeUser('other_request', 2002, 'passenger');

        AyanRequest::create([
            'passenger_id' => $other->id,
            'from_address' => 'Покровск',
            'to_address' => 'Якутск',
            'date' => '2026-04-26',
            'time' => '13:00',
            'description' => 'other request',
            'status' => 'open',
        ]);

        Sanctum::actingAs($owner);

        $created = $this->postJson('/api/ayan/requests', [
            'from_address' => 'Якутск',
            'to_address' => 'Тулагино',
            'date' => '2026-04-24',
            'time' => '18:30',
            'description' => 'persisted request',
        ])->assertCreated();

        $requestId = $created->json('data.id');

        $this->getJson('/api/ayan/requests?to=Тулаг&date=2026-04-24')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $requestId)
            ->assertJsonPath('data.0.passenger.id', $owner->id)
            ->assertJsonPath('data.0.passenger.name', $owner->first_name);

        $this->getJson("/api/ayan/requests/{$requestId}")
            ->assertOk()
            ->assertJsonPath('data.description', 'persisted request');

        $this->patchJson("/api/ayan/requests/{$requestId}", [
            'status' => 'closed',
            'description' => 'updated request',
        ])->assertOk()
            ->assertJsonPath('data.status', 'closed')
            ->assertJsonPath('data.description', 'updated request');

        $this->getJson('/api/ayan/my/requests')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $requestId);
    }

    public function test_response_endpoints_use_persisted_data_for_trip_and_request(): void
    {
        $tripOwner = $this->makeUser('trip_owner', 3001, 'driver');
        $tripResponder = $this->makeUser('trip_responder', 3002, 'passenger');
        $requestOwner = $this->makeUser('request_owner', 3003, 'passenger');
        $requestResponder = $this->makeUser('request_responder', 3004, 'driver');

        $trip = Trip::create([
            'driver_id' => $tripOwner->id,
            'from_address' => 'Якутск',
            'to_address' => 'Намцы',
            'date' => '2026-04-24',
            'time' => '09:00',
            'seats' => 3,
            'price' => 500,
            'comment' => null,
            'status' => 'open',
        ]);

        $request = AyanRequest::create([
            'passenger_id' => $requestOwner->id,
            'from_address' => 'Якутск',
            'to_address' => 'Тулагино',
            'date' => '2026-04-24',
            'time' => '17:00',
            'description' => null,
            'status' => 'open',
        ]);

        Sanctum::actingAs($tripResponder);
        $tripResponse = $this->postJson("/api/ayan/trips/{$trip->id}/responses", [
            'message' => 'trip response',
        ])->assertCreated();

        Sanctum::actingAs($requestResponder);
        $requestResponse = $this->postJson("/api/ayan/requests/{$request->id}/responses", [
            'message' => 'request response',
        ])->assertCreated();

        $tripResponseId = $tripResponse->json('data.id');
        $requestResponseId = $requestResponse->json('data.id');

        Sanctum::actingAs($tripOwner);
        $this->getJson("/api/ayan/trips/{$trip->id}/responses")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $tripResponseId)
            ->assertJsonPath('data.0.user.id', $tripResponder->id);

        $this->patchJson("/api/ayan/responses/{$tripResponseId}", [
            'status' => 'accepted',
        ])->assertOk()
            ->assertJsonPath('data.status', 'accepted');

        Sanctum::actingAs($requestOwner);
        $this->getJson("/api/ayan/requests/{$request->id}/responses")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $requestResponseId)
            ->assertJsonPath('data.0.user.id', $requestResponder->id);

        $this->patchJson("/api/ayan/responses/{$requestResponseId}", [
            'status' => 'rejected',
        ])->assertOk()
            ->assertJsonPath('data.status', 'rejected');

        Sanctum::actingAs($tripResponder);
        $this->getJson('/api/ayan/my/responses')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $tripResponseId,
                'status' => 'accepted',
            ]);

        Sanctum::actingAs($requestResponder);
        $this->deleteJson("/api/ayan/responses/{$requestResponseId}")
            ->assertOk()
            ->assertJsonPath('data.deleted', true);

        $this->assertDatabaseMissing('responses', [
            'id' => $requestResponseId,
        ]);
        $this->assertDatabaseHas('responses', [
            'id' => $tripResponseId,
            'status' => 'accepted',
        ]);
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
