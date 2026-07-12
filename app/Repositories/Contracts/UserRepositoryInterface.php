<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use App\Models\Gardens;

interface UserRepositoryInterface
{
    public function create(array $data): User;
    public function findByEmail(string $email): ?User;
    public function findByUsername(string $username): ?User;
    public function existsByEmail(string $email): bool;
    public function createGardenForUser(User $user, array $data): Gardens;
    public function updateExp(User $user, int $exp): User;
    public function updateLevel(User $user, int $level): User;
    public function currentLevel(User $user): int;
    public function currentExp(User $user): int;
    public function incrementStreak(User $user): User;
    public function resetStreak(User $user): User;
}
