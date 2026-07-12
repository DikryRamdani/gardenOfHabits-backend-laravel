<?php

namespace App\Services\Gamification;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class LevelService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function checkLevelUp(User $user): array
    {
        $leveledUp = false;

        while(true) {
            # dapatkan tingkat level user saat ini via repository
            $currentLevel = $this->userRepository->currentLevel($user);
            # dapatkan jumlah exp user saat ini via repository
            $currentExp = $this->userRepository->currentExp($user);

            # hitung target minimal exp untuk naik ke level berikutnya
            $expReqForNextLevel = $this->getExpReqForNextLevel($currentLevel);

            # proses kenaikan level jika akumulasi exp mencukupi
            if($currentExp >= $expReqForNextLevel) {
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
