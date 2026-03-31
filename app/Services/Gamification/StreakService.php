<?php

namespace App\Services\Gamification;

use App\Models\User;
use App\Repositories\UserRepository;

class StreakService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Dipanggil setiap kali user complete 1 task.
     * - Jika streak_expired_at sudah lewat → reset ke 0 dulu
     * - Increment streak_count + 1
     * - Refresh streak_expired_at = now + 60 menit
     */
    public function updateStreak(User $user): array
    {
        $streakBroken = false;

        // Streak hangus jika waktu expired sudah lewat
        if ($user->streak_expired_at && now()->isAfter($user->streak_expired_at)) {
            $this->userRepository->resetStreak($user);
            $user->refresh();
            $streakBroken = true;
        }

        $user = $this->userRepository->incrementStreak($user);

        return [
            'streak_count'      => $user->streak_count,
            'streak_expired_at' => $user->streak_expired_at,
            'streak_broken'     => $streakBroken,
        ];
    }

    public function resetStreak(User $user): array
    {
        $user = $this->userRepository->resetStreak($user);

        return [
            'streak_count'      => $user->streak_count,
            'streak_expired_at' => $user->streak_expired_at,
        ];
    }

    public function checkAndResetStreak(User $user): User
    {
        if ($user->streak_expired_at && now()->isAfter($user->streak_expired_at)) {
            $user = $this->userRepository->resetStreak($user);
        }
        return $user;
    }

    public function getStreakMultiplier(int $streak): float
    {
        if ($streak >= 15){
            return 2.0;  // +100%
        }else if ($streak >= 10) {
            return 1.5;  // +50%
        }else if ($streak >= 5) {
            return 1.25; // +25%
        }else if ($streak >= 1) {
            return 1.1;  // +10%
        }else{
            return 1.0;
        }
    }
}

