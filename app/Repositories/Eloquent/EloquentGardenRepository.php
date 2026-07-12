<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Models\Gardens;
use App\Repositories\Contracts\GardenRepositoryInterface;

class EloquentGardenRepository implements GardenRepositoryInterface
{
    public function getGardenByUser(User $user): ?Gardens
    {
        # ambil relasi data kebun (garden) milik user
        return $user->garden;
    }

    public function updateHp(Gardens $garden, int $hp): Gardens
    {
        # hitung dan batasi nilai HP baru kebun antara rentang 0-100
        $newHp = max(0, min(100, $garden->hp + $hp));
        
        # perbarui nilai HP kebun di database
        $garden->update(['hp' => $newHp]);
        
        return $garden->fresh();
    }

    public function updateLastDecayCheck(Gardens $garden, $timestamp): Gardens
    {
        # perbarui penanda waktu terakhir pemeriksaan decay kebun
        $garden->update([
            'last_decay_check' => $timestamp,
        ]);
        
        return $garden->fresh();
    }

    public function updatePlantStage(Gardens $garden, string $stage): Gardens
    {
        # perbarui fase pertumbuhan tanaman di kebun
        $garden->update([
            'plant_stage' => $stage,
        ]);
        
        return $garden->fresh();
    }
}
