<?php

namespace App\Services\Gamification;

use App\Models\User;
use App\Repositories\GardenRepository;

class GardenService
{
    private GardenRepository $gardenRepository;

    public function __construct(GardenRepository $gardenRepository)
    {
        $this->gardenRepository = $gardenRepository;
    }

    public function applyDecay(User $user)
    {
        $garden = $this->gardenRepository->getGardenByUser($user);

        if (!$garden) return null;

        if (!$garden->last_decay_check) {
            return $this->gardenRepository->updateLastDecayCheck($garden, now());
        }

        $diffInHours = now()->diffInHours($garden->last_decay_check);
        $diffInDays  = (int) ($diffInHours / 24);

        if ($diffInDays > 0) {
            $garden = $this->gardenRepository->updateHp($garden, -$diffInDays * 10);
            
            // Only increment by the full days we processed, preserving the "extra hours" for the next check
            $newCheckDate = \Carbon\Carbon::parse($garden->last_decay_check)->addDays($diffInDays);
            $garden = $this->gardenRepository->updateLastDecayCheck($garden, $newCheckDate);
        }

        return $garden;
    }

    public function addHp(User $user, string $difficulty): void
    {
        $garden = $this->gardenRepository->getGardenByUser($user);

        if (!$garden) return;

        $hpMapping = ['easy' => 5, 'medium' => 10, 'hard' => 20];
        $this->gardenRepository->updateHp($garden, $hpMapping[$difficulty] ?? 5);
    }

    public function syncPlantStage(User $user): void
    {
        $garden = $this->gardenRepository->getGardenByUser($user);

        if (!$garden) return;

        $newStage    = $this->resolveStage($user->level);
        $stageOrder  = ['seed' => 0, 'sprout' => 1, 'tree' => 2];
        $currentOrder = $stageOrder[$garden->plant_stage] ?? 0;
        $newOrder     = $stageOrder[$newStage] ?? 0;

        if ($newOrder > $currentOrder) {
            $this->gardenRepository->updatePlantStage($garden, $newStage);
        }
    }

    private function resolveStage(int $level): string
    {
        if ($level >= 35) return 'tree';
        if ($level >= 15) return 'sprout';
        return 'seed';
    }
}
