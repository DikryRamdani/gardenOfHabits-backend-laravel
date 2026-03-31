<?php

namespace App\Services\Gamification;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\TaskRepository;
use App\Services\Gamification\LevelService;
use App\Services\Gamification\StreakService;

class ExpService
{
    private UserRepository $userRepository;
    private TaskRepository $taskRepository;
    private LevelService $levelService;
    private StreakService $streakService;

    public function __construct(UserRepository $userRepository, TaskRepository $taskRepository, LevelService $levelService, StreakService $streakService)
    {
        $this->userRepository = $userRepository;
        $this->taskRepository = $taskRepository;
        $this->levelService   = $levelService;
        $this->streakService  = $streakService;
    }

    public function addExp(User $user, string $difficulty): array
    {
        $expMapping = [
            'easy'   => 10,
            'medium' => 20,
            'hard'   => 40,
        ];
        $baseExp    = $expMapping[$difficulty] ?? 10;
        $multiplier = $this->streakService->getStreakMultiplier($user->streak_count ?? 0);
        $expGained  = (int) ($baseExp * $multiplier);

        $user        = $this->userRepository->updateExp($user, $expGained);
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
