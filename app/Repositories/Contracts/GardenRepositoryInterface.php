<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use App\Models\Gardens;

interface GardenRepositoryInterface
{
    public function getGardenByUser(User $user): ?Gardens;
    public function updateHp(Gardens $garden, int $hp): Gardens;
    public function updateLastDecayCheck(Gardens $garden, $timestamp): Gardens;
    public function updatePlantStage(Gardens $garden, string $stage): Gardens;
}
