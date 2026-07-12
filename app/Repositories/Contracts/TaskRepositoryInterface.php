<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use App\Models\Tasks;

interface TaskRepositoryInterface
{
    public function getAllByUser(User $user, ?string $status = null);
    public function createForUser(User $user, array $data): Tasks;
    public function findByUserAndId(User $user, $id): ?Tasks;
    public function getDifficulty(User $user): ?Tasks;
    public function updateTask(Tasks $task, array $data): Tasks;
    public function deleteTask(Tasks $task): bool;
    public function markCompleted(Tasks $task): Tasks;
}
