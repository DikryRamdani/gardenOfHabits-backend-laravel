<?php

namespace App\Services\Gamification;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class StreakService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function updateStreak(User $user): array
    {
        $streakBroken = false;

        # atur ulang streak user ke nol jika masa tenggang sudah kedaluwarsa
        if ($user->streak_expired_at && now()->isAfter($user->streak_expired_at)) {
            $this->userRepository->resetStreak($user);
            $user->refresh();
            $streakBroken = true;
        }

        # tambahkan jumlah streak harian user via repository
        $user = $this->userRepository->incrementStreak($user);

        return [
            'streak_count'      => $user->streak_count,
            'streak_expired_at' => $user->streak_expired_at,
            'streak_broken'     => $streakBroken,
        ];
    }

    public function resetStreak(User $user): array
    {
        # kosongkan catatan streak user via repository
        $user = $this->userRepository->resetStreak($user);

        return [
            'streak_count'      => $user->streak_count,
            'streak_expired_at' => $user->streak_expired_at,
        ];
    }

    public function checkAndResetStreak(User $user): User
    {
        # periksa dan reset streak jika sudah melewati tenggat waktu
        if ($user->streak_expired_at && now()->isAfter($user->streak_expired_at)) {
            $user = $this->userRepository->resetStreak($user);
        }
        return $user;
    }

    public function getStreakMultiplier(int $streak): float
    {
        if ($streak >= 15){
            return 2.0;
        }else if ($streak >= 10) {
            return 1.5;
        }else if ($streak >= 5) {
            return 1.25;
        }else if ($streak >= 1) {
            return 1.1;
        }else{
            return 1.0;
        }
    }
}
