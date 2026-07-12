<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Models\RefreshToken;
use App\Repositories\Contracts\RefreshTokenRepositoryInterface;
use Illuminate\Support\Str;

class EloquentRefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    public function create(User $user): RefreshToken
    {
        # hapus token refresh lama milik user dari database
        RefreshToken::where('user_id', $user->id)->delete();

        # buat token refresh baru yang berlaku selama 30 hari
        return RefreshToken::create([
            'user_id'    => $user->id,
            'token'      => Str::random(64),
            'expires_at' => now()->addDays(30),
        ]);
    }

    public function findValid(string $token): ?RefreshToken
    {
        # cari token refresh terdaftar yang masa berlakunya belum kedaluwarsa
        return RefreshToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();
    }

    public function delete(string $token): void
    {
        # hapus token refresh tertentu berdasarkan nilai token-nya
        RefreshToken::where('token', $token)->delete();
    }

    public function deleteByUser(User $user): void
    {
        # hapus seluruh token refresh milik user tertentu
        RefreshToken::where('user_id', $user->id)->delete();
    }
}
