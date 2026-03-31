<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\RefreshToken;
use Illuminate\Support\Str;

class RefreshTokenRepository
{
    public function create(User $user): RefreshToken
    {
        RefreshToken::where('user_id', $user->id)->delete();

        return RefreshToken::create([
            'user_id'    => $user->id,
            'token'      => Str::random(64),
            'expires_at' => now()->addDays(30),
        ]);
    }

    public function findValid(string $token): ?RefreshToken
    {
        return RefreshToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();
    }

    public function delete(string $token): void
    {
        RefreshToken::where('token', $token)->delete();
    }

    public function deleteByUser(User $user): void
    {
        RefreshToken::where('user_id', $user->id)->delete();
    }
}
