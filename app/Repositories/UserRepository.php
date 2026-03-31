<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Gardens;

use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function create(array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return User::create($data);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findByUsername(string $username): ?User
    {
        return User::where('username', $username)->first();
    }

    public function existsByEmail(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    public function createGardenForUser(User $user, array $data): Gardens
    {
        return $user->garden()->create([
            'hp' => $data['hp'] ?? 100,
        ]);
    }

    public function updateExp(User $user, int $exp): User
    {
        $user->update([
            'total_exp' => $user->total_exp + $exp,
        ]);

        return $user->fresh();
    }

    public function updateLevel(User $user, int $level): User
    {
        $user->update([
            'level' => $user->level + $level,
        ]);

        return $user->fresh();
    }

    public function currentLevel(User $user): int
    {
        return $user->level;
    }

    public function currentExp(User $user): int
    {
        return $user->total_exp;
    }

    public function incrementStreak(User $user): User
    {
        $user->update([
            'streak_count'      => $user->streak_count + 1,
            'streak_expired_at' => now()->addMinutes(60),
        ]);

        return $user->fresh();
    }

    public function resetStreak(User $user): User
    {
        $user->update([
            'streak_count'      => 0,
            'streak_expired_at' => null,
        ]);

        return $user->fresh();
    }

}