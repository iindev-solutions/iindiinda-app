<?php

namespace Tests\Feature;

use App\Models\TalBooking;
use App\Models\TalMaster;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TalPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_master_endpoints_use_persisted_data(): void
    {
        $owner = $this->makeUser('tal_master_owner', 9801, 'master');
        $other = $this->makeUser('tal_master_other', 9802, 'master');

        TalMaster::create([
            'master_id' => $other->id,
            'category' => 'home',
            'service_label' => 'Other service',
            'description' => 'other card',
            'location' => 'Center',
            'availability_status' => 'busy',
            'available_note' => null,
            'price_from' => null,
            'status' => 'open',
        ]);

        Sanctum::actingAs($owner);

        $created = $this->postJson('/api/tal/masters', [
            'category' => 'beauty',
            'service_label' => 'Haircut',
            'description' => 'Short haircut today',
            'location' => 'DSK district',
            'availability_status' => 'later',
            'available_note' => 'After 18:00',
            'price_from' => 1500,
        ])->assertCreated();

        $masterId = $created->json('data.id');

        $this->getJson('/api/tal/masters?category=beauty&availability_status=later&location=DSK')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $masterId)
            ->assertJsonPath('data.0.master.id', $owner->id)
            ->assertJsonPath('data.0.master.name', $owner->first_name);

        $this->getJson("/api/tal/masters/{$masterId}")
            ->assertOk()
            ->assertJsonPath('data.service_label', 'Haircut');

        $this->patchJson("/api/tal/masters/{$masterId}", [
            'availability_status' => 'tomorrow',
            'price_from' => 1700,
            'available_note' => 'Tomorrow morning',
        ])->assertOk()
            ->assertJsonPath('data.availability_status', 'tomorrow')
            ->assertJsonPath('data.price_from', 1700)
            ->assertJsonPath('data.available_note', 'Tomorrow morning');

        $this->getJson('/api/tal/my/masters')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $masterId);
    }

    public function test_booking_endpoints_use_persisted_data_and_final_lifecycle(): void
    {
        $owner = $this->makeUser('tal_owner', 9901, 'master');
        $acceptedClient = $this->makeUser('tal_client_a', 9902, 'passenger');
        $rejectedClient = $this->makeUser('tal_client_b', 9903, 'driver');

        $master = TalMaster::create([
            'master_id' => $owner->id,
            'category' => 'beauty',
            'service_label' => 'Fade cut',
            'description' => 'Available this evening',
            'location' => 'Yakutsk',
            'availability_status' => 'later',
            'available_note' => 'After 18:30',
            'price_from' => 1800,
            'status' => 'open',
        ]);

        Sanctum::actingAs($acceptedClient);
        $accepted = $this->postJson("/api/tal/masters/{$master->id}/bookings", [
            'message' => 'Need a haircut today',
            'desired_time' => 'After 19:00',
        ])->assertCreated();

        Sanctum::actingAs($rejectedClient);
        $rejected = $this->postJson("/api/tal/masters/{$master->id}/bookings", [
            'desired_time' => '20:00',
        ])->assertCreated();

        $acceptedId = $accepted->json('data.id');
        $rejectedId = $rejected->json('data.id');

        Sanctum::actingAs($owner);
        $this->getJson("/api/tal/masters/{$master->id}/bookings")
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->patchJson("/api/tal/bookings/{$acceptedId}", [
            'status' => 'accepted',
        ])->assertOk()
            ->assertJsonPath('data.status', 'accepted');

        $this->patchJson("/api/tal/masters/{$master->id}", [
            'status' => 'completed',
        ])->assertOk()
            ->assertJsonPath('data.status', 'completed');

        Sanctum::actingAs($acceptedClient);
        $this->getJson('/api/tal/my/bookings')
            ->assertOk()
            ->assertJsonPath('data.0.id', $acceptedId)
            ->assertJsonPath('data.0.tal_master.id', $master->id)
            ->assertJsonPath('data.0.tal_master.status', 'completed');

        $this->deleteJson("/api/tal/bookings/{$acceptedId}")
            ->assertStatus(422);

        $this->assertDatabaseHas('tal_bookings', [
            'id' => $acceptedId,
            'status' => 'accepted',
        ]);
        $this->assertDatabaseHas('tal_bookings', [
            'id' => $rejectedId,
            'status' => 'rejected',
        ]);
    }

    public function test_role_busy_duplicate_and_pending_delete_guards_work(): void
    {
        $notMaster = $this->makeUser('tal_not_master', 9951, 'passenger');
        $owner = $this->makeUser('tal_guard_owner', 9952, 'master');
        $responder = $this->makeUser('tal_guard_responder', 9953, 'sender');

        Sanctum::actingAs($notMaster);
        $this->postJson('/api/tal/masters', [
            'category' => 'beauty',
            'service_label' => 'Nope',
            'description' => 'forbidden',
            'location' => 'Yakutsk',
            'availability_status' => 'now',
        ])->assertForbidden();

        $busyMaster = TalMaster::create([
            'master_id' => $owner->id,
            'category' => 'beauty',
            'service_label' => 'Busy barber',
            'description' => 'busy now',
            'location' => 'Center',
            'availability_status' => 'busy',
            'available_note' => null,
            'price_from' => null,
            'status' => 'open',
        ]);

        $openMaster = TalMaster::create([
            'master_id' => $owner->id,
            'category' => 'beauty',
            'service_label' => 'Open barber',
            'description' => 'open now',
            'location' => 'Center',
            'availability_status' => 'now',
            'available_note' => null,
            'price_from' => null,
            'status' => 'open',
        ]);

        Sanctum::actingAs($responder);
        $this->postJson("/api/tal/masters/{$busyMaster->id}/bookings", [
            'message' => 'try busy',
        ])->assertStatus(422)
            ->assertJsonPath('message', 'Master is busy');

        $created = $this->postJson("/api/tal/masters/{$openMaster->id}/bookings", [
            'message' => 'first try',
        ])->assertCreated();

        $bookingId = $created->json('data.id');

        $this->postJson("/api/tal/masters/{$openMaster->id}/bookings", [
            'message' => 'second try',
        ])->assertStatus(422);

        $this->deleteJson("/api/tal/bookings/{$bookingId}")
            ->assertOk();
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
