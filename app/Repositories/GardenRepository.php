<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Gardens;

class GardenRepository
{
    public function getGardenByUser(User $user): ?Gardens
    {
        return $user->garden;
    }

    public function updateHp(Gardens $garden, int $hp): Gardens
    {
        $newHp = max(0, min(100, $garden->hp + $hp));
        $garden->update(['hp' => $newHp]);
        return $garden->fresh();
    }


    public function updateLastDecayCheck(Gardens $garden, $timestamp): Gardens
    {
        $garden->update([
            'last_decay_check' => $timestamp,
        ]);
        return $garden->fresh();
    }

    public function updatePlantStage(Gardens $garden, string $stage): Gardens
    {
        $garden->update([
            'plant_stage' => $stage,
        ]);
        return $garden->fresh();
    }
}
