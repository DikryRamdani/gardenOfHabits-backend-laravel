<?php

namespace App\Services\Gamification;
use App\Models\User;
use App\Repositories\UserRepository;

class LevelService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function checkLevelUp(User $user): array
    {
        $leveledUp = false;

        while(true) {
            $currentLevel = $this->userRepository->currentLevel($user);
            $currentExp = $this->userRepository->currentExp($user);

            $expReqForNextLevel = $this->getExpReqForNextLevel($currentLevel);

            if($currentExp >= $expReqForNextLevel) {
                // Keep the current multiplier when decreasing EXP for leveling up
                $user = $this->userRepository->updateLevel($user, 1);
                $user = $this->userRepository->updateExp($user, -$expReqForNextLevel);
                $leveledUp = true;
            }else{
                break;
            }
        }

        return [
            'leveled_up' => $leveledUp,
            'level' => $user->level,
            'total_exp' => $user->total_exp,
        ];
    }

    public function getExpReqForNextLevel(int $currentLevel): int
    {
        if ($currentLevel <= 10) return 150 + (50 * ($currentLevel - 1));
        if ($currentLevel <= 20) return 700 + (100 * ($currentLevel - 11));
        return 1900 + (200 * ($currentLevel - 21));
    }
}
