<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Models\Gardens;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function create(array $data): User
    {
        if (isset($data['password'])) {
            # enkripsi password user baru
            $data['password'] = Hash::make($data['password']);
        }
        
        # simpan data user baru ke database
        return User::create($data);
    }

    public function findByEmail(string $email): ?User
    {
        # cari user berdasarkan alamat email
        return User::where('email', $email)->first();
    }

    public function findByUsername(string $username): ?User
    {
        # cari user berdasarkan username
        return User::where('username', $username)->first();
    }

    public function existsByEmail(string $email): bool
    {
        # periksa apakah alamat email sudah terdaftar
        return User::where('email', $email)->exists();
    }

    public function createGardenForUser(User $user, array $data): Gardens
    {
        # buat kebun (garden) baru terhubung dengan user
        return $user->garden()->create([
            'hp' => $data['hp'] ?? 100,
        ]);
    }

    public function updateExp(User $user, int $exp): User
    {
        # perbarui akumulasi exp user
        $user->update([
            'total_exp' => $user->total_exp + $exp,
        ]);

        return $user->fresh();
    }

    public function updateLevel(User $user, int $level): User
    {
        # perbarui level user
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
        # tambahkan jumlah streak harian user dan atur masa berlaku
        $user->update([
            'streak_count'      => $user->streak_count + 1,
            'streak_expired_at' => now()->addMinutes(60),
        ]);

        return $user->fresh();
    }

    public function resetStreak(User $user): User
    {
        # atur ulang streak user menjadi nol
        $user->update([
            'streak_count'      => 0,
            'streak_expired_at' => null,
        ]);

        return $user->fresh();
    }
}
