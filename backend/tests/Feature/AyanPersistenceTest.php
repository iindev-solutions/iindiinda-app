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
        $tripDate = now()->addDay()->toDateString();
        $otherTripDate = now()->addDays(2)->toDateString();

        Trip::create([
            'driver_id' => $other->id,
            'from_address' => 'Намцы',
            'to_address' => 'Якутск',
            'date' => $otherTripDate,
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
            'date' => $tripDate,
            'time' => '09:15',
            'seats' => 3,
            'price' => 500,
            'comment' => 'persisted trip',
        ])->assertCreated();

        $tripId = $created->json('data.id');

        $this->getJson("/api/ayan/trips?from=Якут&date={$tripDate}")
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
        $requestDate = now()->addDay()->toDateString();
        $otherRequestDate = now()->addDays(3)->toDateString();

        AyanRequest::create([
            'passenger_id' => $other->id,
            'from_address' => 'Покровск',
            'to_address' => 'Якутск',
            'date' => $otherRequestDate,
            'time' => '13:00',
            'description' => 'other request',
            'status' => 'open',
        ]);

        Sanctum::actingAs($owner);

        $created = $this->postJson('/api/ayan/requests', [
            'from_address' => 'Якутск',
            'to_address' => 'Тулагино',
            'date' => $requestDate,
            'time' => '18:30',
            'description' => 'persisted request',
        ])->assertCreated();

        $requestId = $created->json('data.id');

        $this->getJson("/api/ayan/requests?to=Тулаг&date={$requestDate}")
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

    public function test_public_feeds_hide_past_open_items_but_my_and_detail_keep_them(): void
    {
        $driver = $this->makeUser('driver_past', 2501, 'driver');
        $passenger = $this->makeUser('passenger_past', 2502, 'passenger');
        $today = now()->toDateString();

        $pastTrip = Trip::create([
            'driver_id' => $driver->id,
            'from_address' => 'Якутск',
            'to_address' => 'Намцы',
            'date' => now()->subDay()->toDateString(),
            'time' => '09:00',
            'seats' => 2,
            'price' => 0,
            'comment' => 'past trip',
            'status' => 'open',
        ]);

        $futureTrip = Trip::create([
            'driver_id' => $driver->id,
            'from_address' => 'Якутск',
            'to_address' => 'Покровск',
            'date' => now()->addDay()->toDateString(),
            'time' => '10:00',
            'seats' => 3,
            'price' => 700,
            'comment' => 'future trip',
            'status' => 'open',
        ]);

        Trip::create([
            'driver_id' => $driver->id,
            'from_address' => 'Марха',
            'to_address' => 'Якутск',
            'date' => $today,
            'time' => now()->subHour()->format('H:i'),
            'seats' => 1,
            'price' => 300,
            'comment' => 'today past trip',
            'status' => 'open',
        ]);

        Trip::create([
            'driver_id' => $driver->id,
            'from_address' => 'Хатассы',
            'to_address' => 'Якутск',
            'date' => $today,
            'time' => now()->addHour()->format('H:i'),
            'seats' => 1,
            'price' => 300,
            'comment' => 'today future trip',
            'status' => 'open',
        ]);

        $pastRequest = AyanRequest::create([
            'passenger_id' => $passenger->id,
            'from_address' => 'Намцы',
            'to_address' => 'Якутск',
            'date' => now()->subDay()->toDateString(),
            'time' => '12:00',
            'description' => 'past request',
            'status' => 'open',
        ]);

        $futureRequest = AyanRequest::create([
            'passenger_id' => $passenger->id,
            'from_address' => 'Тулагино',
            'to_address' => 'Якутск',
            'date' => now()->addDays(2)->toDateString(),
            'time' => '14:00',
            'description' => 'future request',
            'status' => 'open',
        ]);

        AyanRequest::create([
            'passenger_id' => $passenger->id,
            'from_address' => 'Тулагино',
            'to_address' => 'Якутск',
            'date' => $today,
            'time' => now()->subHour()->format('H:i'),
            'description' => 'today past request',
            'status' => 'open',
        ]);

        AyanRequest::create([
            'passenger_id' => $passenger->id,
            'from_address' => 'Тулагино',
            'to_address' => 'Якутск',
            'date' => $today,
            'time' => now()->addHour()->format('H:i'),
            'description' => 'today future request',
            'status' => 'open',
        ]);

        Sanctum::actingAs($driver);
        $this->getJson('/api/ayan/trips')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['id' => $futureTrip->id]);

        $this->getJson('/api/ayan/my/trips')
            ->assertOk()
            ->assertJsonCount(4, 'data');

        $this->getJson("/api/ayan/trips/{$pastTrip->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $pastTrip->id);

        Sanctum::actingAs($passenger);
        $this->getJson('/api/ayan/requests')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['id' => $futureRequest->id]);

        $this->getJson('/api/ayan/my/requests')
            ->assertOk()
            ->assertJsonCount(4, 'data');

        $this->getJson("/api/ayan/requests/{$pastRequest->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $pastRequest->id);
    }

    public function test_response_endpoints_use_persisted_data_for_trip_and_request(): void
    {
        $tripOwner = $this->makeUser('trip_owner', 3001, 'driver');
        $tripResponder = $this->makeUser('trip_responder', 3002, 'passenger');
        $requestOwner = $this->makeUser('request_owner', 3003, 'passenger');
        $requestResponder = $this->makeUser('request_responder', 3004, 'driver');
        $tripDate = now()->addDay()->toDateString();
        $requestDate = now()->addDays(2)->toDateString();

        $trip = Trip::create([
            'driver_id' => $tripOwner->id,
            'from_address' => 'Якутск',
            'to_address' => 'Намцы',
            'date' => $tripDate,
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
            'date' => $requestDate,
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

    public function test_trip_and_request_response_lists_are_owner_only(): void
    {
        $tripOwner = $this->makeUser('trip_owner_list', 4001, 'driver');
        $tripResponder = $this->makeUser('trip_responder_list', 4002, 'passenger');
        $requestOwner = $this->makeUser('request_owner_list', 4003, 'passenger');
        $requestResponder = $this->makeUser('request_responder_list', 4004, 'driver');
        $tripDate = now()->addDay()->toDateString();
        $requestDate = now()->addDays(2)->toDateString();

        $trip = Trip::create([
            'driver_id' => $tripOwner->id,
            'from_address' => 'Якутск',
            'to_address' => 'Намцы',
            'date' => $tripDate,
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
            'date' => $requestDate,
            'time' => '17:00',
            'description' => null,
            'status' => 'open',
        ]);

        AyanResponse::create([
            'user_id' => $tripResponder->id,
            'trip_id' => $trip->id,
            'request_id' => null,
            'message' => 'trip response',
            'status' => 'pending',
        ]);

        AyanResponse::create([
            'user_id' => $requestResponder->id,
            'trip_id' => null,
            'request_id' => $request->id,
            'message' => 'request response',
            'status' => 'pending',
        ]);

        Sanctum::actingAs($tripResponder);
        $this->getJson("/api/ayan/trips/{$trip->id}/responses")->assertForbidden();

        Sanctum::actingAs($requestResponder);
        $this->getJson("/api/ayan/requests/{$request->id}/responses")->assertForbidden();
    }

    public function test_role_rules_are_enforced_for_trip_request_and_response_creation(): void
    {
        $passenger = $this->makeUser('passenger_role', 5001, 'passenger');
        $driver = $this->makeUser('driver_role', 5002, 'driver');
        $tripOwner = $this->makeUser('trip_owner_role', 5003, 'driver');
        $requestOwner = $this->makeUser('request_owner_role', 5004, 'passenger');
        $tripDate = now()->addDay()->toDateString();
        $requestDate = now()->addDays(2)->toDateString();

        $trip = Trip::create([
            'driver_id' => $tripOwner->id,
            'from_address' => 'Якутск',
            'to_address' => 'Намцы',
            'date' => $tripDate,
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
            'date' => $requestDate,
            'time' => '17:00',
            'description' => null,
            'status' => 'open',
        ]);

        Sanctum::actingAs($passenger);
        $this->postJson('/api/ayan/trips', [
            'from_address' => 'Якутск',
            'to_address' => 'Намцы',
            'date' => $tripDate,
            'time' => '09:15',
            'seats' => 3,
            'price' => 500,
        ])->assertForbidden();

        Sanctum::actingAs($driver);
        $this->postJson('/api/ayan/requests', [
            'from_address' => 'Якутск',
            'to_address' => 'Тулагино',
            'date' => $requestDate,
        ])->assertForbidden();

        Sanctum::actingAs($driver);
        $this->postJson("/api/ayan/trips/{$trip->id}/responses", [
            'message' => 'wrong role',
        ])->assertForbidden();

        Sanctum::actingAs($passenger);
        $this->postJson("/api/ayan/requests/{$request->id}/responses", [
            'message' => 'wrong role',
        ])->assertForbidden();
    }

    public function test_duplicate_and_closed_target_responses_are_rejected(): void
    {
        $tripOwner = $this->makeUser('trip_owner_rules', 6001, 'driver');
        $passenger = $this->makeUser('trip_passenger_rules', 6002, 'passenger');
        $requestOwner = $this->makeUser('request_owner_rules', 6003, 'passenger');
        $driver = $this->makeUser('request_driver_rules', 6004, 'driver');
        $today = now()->toDateString();
        $tripDate = now()->addDay()->toDateString();
        $closedTripDate = now()->addDays(2)->toDateString();
        $requestDate = now()->addDays(3)->toDateString();
        $closedRequestDate = now()->addDays(4)->toDateString();

        $trip = Trip::create([
            'driver_id' => $tripOwner->id,
            'from_address' => 'Якутск',
            'to_address' => 'Намцы',
            'date' => $tripDate,
            'time' => '09:00',
            'seats' => 3,
            'price' => 500,
            'comment' => null,
            'status' => 'open',
        ]);

        $closedTrip = Trip::create([
            'driver_id' => $tripOwner->id,
            'from_address' => 'Покровск',
            'to_address' => 'Якутск',
            'date' => $closedTripDate,
            'time' => '10:00',
            'seats' => 1,
            'price' => 700,
            'comment' => null,
            'status' => 'closed',
        ]);

        $pastTrip = Trip::create([
            'driver_id' => $tripOwner->id,
            'from_address' => 'Марха',
            'to_address' => 'Якутск',
            'date' => $today,
            'time' => now()->subHour()->format('H:i'),
            'seats' => 1,
            'price' => 200,
            'comment' => null,
            'status' => 'open',
        ]);

        $request = AyanRequest::create([
            'passenger_id' => $requestOwner->id,
            'from_address' => 'Якутск',
            'to_address' => 'Тулагино',
            'date' => $requestDate,
            'time' => '17:00',
            'description' => null,
            'status' => 'open',
        ]);

        $closedRequest = AyanRequest::create([
            'passenger_id' => $requestOwner->id,
            'from_address' => 'Намцы',
            'to_address' => 'Якутск',
            'date' => $closedRequestDate,
            'time' => '18:00',
            'description' => null,
            'status' => 'closed',
        ]);

        $pastRequest = AyanRequest::create([
            'passenger_id' => $requestOwner->id,
            'from_address' => 'Тулагино',
            'to_address' => 'Якутск',
            'date' => $today,
            'time' => now()->subHour()->format('H:i'),
            'description' => null,
            'status' => 'open',
        ]);

        Sanctum::actingAs($passenger);
        $this->postJson("/api/ayan/trips/{$trip->id}/responses", [
            'message' => 'first',
        ])->assertCreated();

        $this->postJson("/api/ayan/trips/{$trip->id}/responses", [
            'message' => 'duplicate',
        ])->assertStatus(422);

        $this->postJson("/api/ayan/trips/{$closedTrip->id}/responses", [
            'message' => 'closed target',
        ])->assertStatus(422);

        $this->postJson("/api/ayan/trips/{$pastTrip->id}/responses", [
            'message' => 'past target',
        ])->assertStatus(422);

        Sanctum::actingAs($driver);
        $this->postJson("/api/ayan/requests/{$request->id}/responses", [
            'message' => 'first',
        ])->assertCreated();

        $this->postJson("/api/ayan/requests/{$request->id}/responses", [
            'message' => 'duplicate',
        ])->assertStatus(422);

        $this->postJson("/api/ayan/requests/{$closedRequest->id}/responses", [
            'message' => 'closed target',
        ])->assertStatus(422);

        $this->postJson("/api/ayan/requests/{$pastRequest->id}/responses", [
            'message' => 'past target',
        ])->assertStatus(422);
    }

    public function test_only_one_response_can_be_accepted_for_same_trip(): void
    {
        $tripOwner = $this->makeUser('trip_owner_accept', 7001, 'driver');
        $passengerOne = $this->makeUser('trip_passenger_one', 7002, 'passenger');
        $passengerTwo = $this->makeUser('trip_passenger_two', 7003, 'passenger');
        $tripDate = now()->addDay()->toDateString();

        $trip = Trip::create([
            'driver_id' => $tripOwner->id,
            'from_address' => 'Якутск',
            'to_address' => 'Намцы',
            'date' => $tripDate,
            'time' => '09:00',
            'seats' => 3,
            'price' => 500,
            'comment' => null,
            'status' => 'open',
        ]);

        $accepted = AyanResponse::create([
            'user_id' => $passengerOne->id,
            'trip_id' => $trip->id,
            'request_id' => null,
            'message' => 'accepted first',
            'status' => 'accepted',
        ]);

        $other = AyanResponse::create([
            'user_id' => $passengerTwo->id,
            'trip_id' => $trip->id,
            'request_id' => null,
            'message' => 'pending second',
            'status' => 'rejected',
        ]);

        Trip::query()->whereKey($trip->id)->update(['status' => 'closed']);

        Sanctum::actingAs($tripOwner);
        $this->patchJson("/api/ayan/responses/{$other->id}", [
            'status' => 'accepted',
        ])->assertStatus(422);

        $this->assertDatabaseHas('responses', [
            'id' => $accepted->id,
            'status' => 'accepted',
        ]);
        $this->assertDatabaseHas('responses', [
            'id' => $other->id,
            'status' => 'rejected',
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
