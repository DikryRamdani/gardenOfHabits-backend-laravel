<?php

namespace App\Services\Gamification;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Services\Gamification\LevelService;
use App\Services\Gamification\StreakService;

class ExpService
{
    private UserRepositoryInterface $userRepository;
    private TaskRepositoryInterface $taskRepository;
    private LevelService $levelService;
    private StreakService $streakService;

    public function __construct(
        UserRepositoryInterface $userRepository,
        TaskRepositoryInterface $taskRepository,
        LevelService $levelService,
        StreakService $streakService
    ) {
        $this->userRepository = $userRepository;
        $this->taskRepository = $taskRepository;
        $this->levelService   = $levelService;
        $this->streakService  = $streakService;
    }

    public function addExp(User $user, string $difficulty): array
    {
        # tentukan jumlah exp dasar berdasarkan tingkat kesulitan tugas
        $expMapping = [
            'easy'   => 10,
            'medium' => 20,
            'hard'   => 40,
        ];
        $baseExp    = $expMapping[$difficulty] ?? 10;
        # hitung faktor pengali berdasarkan akumulasi streak user via service
        $multiplier = $this->streakService->getStreakMultiplier($user->streak_count ?? 0);
        $expGained  = (int) ($baseExp * $multiplier);

        # perbarui nilai exp user di database via repository
        $user        = $this->userRepository->updateExp($user, $expGained);
        # evaluasi apakah user berhak naik level berdasarkan akumulasi exp terbarunya
        $levelResult = $this->levelService->checkLevelUp($user);

        return [
            'exp_gained'    => $expGained,
            'base_exp'      => $baseExp,
            'multiplier'    => $multiplier,
            'streak_count'  => $user->streak_count,
            'total_exp'     => $user->total_exp,
            'level_up'      => $levelResult['leveled_up'],
            'level'         => $levelResult['level'],
        ];
    }
}
