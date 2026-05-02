<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UusResponse;
use App\Models\UusTask;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UusPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_endpoints_use_persisted_data(): void
    {
        $owner = $this->makeUser('uus_owner', 9501);
        $other = $this->makeUser('uus_other', 9502);
        $taskDate = now()->addDays(2)->toDateString();

        UusTask::create([
            'customer_id' => $other->id,
            'category' => 'repair',
            'description' => 'other task',
            'location' => 'Center',
            'desired_when' => 'date',
            'date' => now()->addDays(3)->toDateString(),
            'budget' => 1000,
            'budget_type' => 'fixed',
            'urgency' => 'normal',
            'response_limit' => 5,
            'status' => 'open',
        ]);

        Sanctum::actingAs($owner);

        $created = $this->postJson('/api/uus/tasks', [
            'category' => 'home',
            'description' => 'Need window cleaning',
            'location' => 'DSK district',
            'desired_when' => 'date',
            'date' => $taskDate,
            'budget' => 2000,
            'budget_type' => 'fixed',
            'urgency' => 'normal',
        ])->assertCreated();

        $taskId = $created->json('data.id');

        $this->getJson("/api/uus/tasks?category=home&location=DSK&urgency=normal&date={$taskDate}")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $taskId)
            ->assertJsonPath('data.0.customer.id', $owner->id)
            ->assertJsonPath('data.0.customer.name', $owner->first_name)
            ->assertJsonPath('data.0.response_limit', 5);

        $this->getJson("/api/uus/tasks/{$taskId}")
            ->assertOk()
            ->assertJsonPath('data.description', 'Need window cleaning');

        $this->patchJson("/api/uus/tasks/{$taskId}", [
            'urgency' => 'urgent',
            'budget_type' => 'negotiable',
            'status' => 'cancelled',
        ])->assertOk()
            ->assertJsonPath('data.urgency', 'urgent')
            ->assertJsonPath('data.response_limit', 3)
            ->assertJsonPath('data.status', 'cancelled')
            ->assertJsonPath('data.budget_type', 'negotiable');

        $this->getJson('/api/uus/my/tasks')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $taskId);
    }

    public function test_response_endpoints_use_persisted_data_and_final_lifecycle(): void
    {
        $owner = $this->makeUser('uus_task_owner', 9601);
        $acceptedResponder = $this->makeUser('uus_responder_a', 9602);
        $rejectedResponder = $this->makeUser('uus_responder_b', 9603);

        $task = UusTask::create([
            'customer_id' => $owner->id,
            'category' => 'repair',
            'description' => 'Assemble cabinet',
            'location' => 'Yakutsk',
            'desired_when' => 'tomorrow',
            'date' => now()->addDay()->toDateString(),
            'budget' => 3000,
            'budget_type' => 'fixed',
            'urgency' => 'normal',
            'response_limit' => 5,
            'status' => 'open',
        ]);

        Sanctum::actingAs($acceptedResponder);
        $accepted = $this->postJson("/api/uus/tasks/{$task->id}/responses", [
            'message' => 'Can do it tomorrow morning',
            'offered_price' => 2800,
        ])->assertCreated();

        Sanctum::actingAs($rejectedResponder);
        $rejected = $this->postJson("/api/uus/tasks/{$task->id}/responses", [
            'message' => 'Can do it in the evening',
        ])->assertCreated();

        $acceptedId = $accepted->json('data.id');
        $rejectedId = $rejected->json('data.id');

        Sanctum::actingAs($owner);
        $this->getJson("/api/uus/tasks/{$task->id}/responses")
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->patchJson("/api/uus/responses/{$acceptedId}", [
            'status' => 'accepted',
        ])->assertOk()
            ->assertJsonPath('data.status', 'accepted');

        $this->patchJson("/api/uus/tasks/{$task->id}", [
            'status' => 'completed',
        ])->assertOk()
            ->assertJsonPath('data.status', 'completed');

        Sanctum::actingAs($acceptedResponder);
        $this->getJson('/api/uus/my/responses')
            ->assertOk()
            ->assertJsonPath('data.0.id', $acceptedId)
            ->assertJsonPath('data.0.task.id', $task->id)
            ->assertJsonPath('data.0.task.status', 'completed');

        $this->deleteJson("/api/uus/responses/{$acceptedId}")
            ->assertStatus(422);

        $this->assertDatabaseHas('uus_responses', [
            'id' => $acceptedId,
            'status' => 'accepted',
        ]);
        $this->assertDatabaseHas('uus_responses', [
            'id' => $rejectedId,
            'status' => 'rejected',
        ]);
    }

    public function test_response_limit_duplicate_guard_and_pending_delete_work(): void
    {
        $owner = $this->makeUser('uus_limit_owner', 9701);
        $duplicateResponder = $this->makeUser('uus_duplicate', 9702);
        $first = $this->makeUser('uus_first', 9703);
        $second = $this->makeUser('uus_second', 9704);
        $third = $this->makeUser('uus_third', 9705);
        $fourth = $this->makeUser('uus_fourth', 9706);

        $normalTask = UusTask::create([
            'customer_id' => $owner->id,
            'category' => 'other',
            'description' => 'Need helper',
            'location' => 'Yakutsk',
            'desired_when' => 'flexible',
            'date' => null,
            'budget' => null,
            'budget_type' => 'negotiable',
            'urgency' => 'normal',
            'response_limit' => 5,
            'status' => 'open',
        ]);

        Sanctum::actingAs($duplicateResponder);
        $created = $this->postJson("/api/uus/tasks/{$normalTask->id}/responses", [
            'offered_price' => 500,
        ])->assertCreated();

        $responseId = $created->json('data.id');

        $this->postJson("/api/uus/tasks/{$normalTask->id}/responses", [
            'message' => 'second try',
        ])->assertStatus(422);

        $this->deleteJson("/api/uus/responses/{$responseId}")
            ->assertOk();

        $urgentTask = UusTask::create([
            'customer_id' => $owner->id,
            'category' => 'home',
            'description' => 'Urgent task',
            'location' => 'DSK',
            'desired_when' => 'today',
            'date' => now()->toDateString(),
            'budget' => 1000,
            'budget_type' => 'fixed',
            'urgency' => 'urgent',
            'response_limit' => 3,
            'status' => 'open',
        ]);

        Sanctum::actingAs($first);
        $this->postJson("/api/uus/tasks/{$urgentTask->id}/responses", ['message' => 'one'])->assertCreated();

        Sanctum::actingAs($second);
        $this->postJson("/api/uus/tasks/{$urgentTask->id}/responses", ['message' => 'two'])->assertCreated();

        Sanctum::actingAs($third);
        $this->postJson("/api/uus/tasks/{$urgentTask->id}/responses", ['message' => 'three'])->assertCreated();

        Sanctum::actingAs($fourth);
        $this->postJson("/api/uus/tasks/{$urgentTask->id}/responses", ['message' => 'four'])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Response limit reached');
    }

    private function makeUser(string $name, int $telegramId): User
    {
        return User::create([
            'telegram_id' => $telegramId,
            'first_name' => $name,
            'username' => $name,
            'role' => 'master',
            'rating' => 5.0,
            'completed_orders' => 0,
            'is_available' => true,
        ]);
    }
}
