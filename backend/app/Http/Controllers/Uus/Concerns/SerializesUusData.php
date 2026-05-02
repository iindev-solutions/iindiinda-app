<?php

namespace App\Http\Controllers\Uus\Concerns;

use App\Models\User;
use App\Models\UusResponse;
use App\Models\UusTask;

trait SerializesUusData
{
    protected function serializeTask(UusTask $task): array
    {
        return [
            'id' => $task->id,
            'customer' => $this->serializeUserSummary($task->customer),
            'category' => $task->category,
            'description' => $task->description,
            'location' => $task->location,
            'desired_when' => $task->desired_when,
            'date' => $task->date,
            'budget' => $task->budget,
            'budget_type' => $task->budget_type,
            'urgency' => $task->urgency,
            'response_limit' => $task->response_limit,
            'status' => $task->status,
            'created_at' => $task->created_at?->toIso8601String(),
        ];
    }

    protected function serializeResponse(UusResponse $response): array
    {
        return [
            'id' => $response->id,
            'task_id' => $response->task_id,
            'task' => $response->relationLoaded('task') && $response->task ? $this->serializeTask($response->task) : null,
            'user' => $this->serializeUserSummary($response->user),
            'message' => $response->message,
            'offered_price' => $response->offered_price,
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
