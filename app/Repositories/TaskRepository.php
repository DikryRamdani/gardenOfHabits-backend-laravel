<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Tasks;
use App\Models\Gardens;

class TaskRepository
{
    public function getAllByUser(User $user, ?string $status = null)
    {
        $query = $user->tasks()->latest();

        if ($status === 'active') {
            $query->where('is_completed', false);
        } elseif ($status === 'completed') {
            $query->where('is_completed', true);
        }

        return $query->get();
    }

    public function createForUser(User $user, array $data): Tasks
    {
        return $user->tasks()->create($data);
    }

    public function findByUserAndId(User $user, $id): ?Tasks
    {
        return $user->tasks()->find($id);
    }

    public function getDifficulty(User $user): ?Tasks
    {
        return $user->tasks()->where('is_completed', false)->first();
    }

    public function updateTask(Tasks $task, array $data): Tasks
    {
        $task->update($data);
        return $task->fresh();
    }

    public function deleteTask(Tasks $task): bool
    {
        return (bool) $task->delete();
    }

    public function markCompleted(Tasks $task): Tasks
    {
        $task->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        return $task->fresh();
    }
}