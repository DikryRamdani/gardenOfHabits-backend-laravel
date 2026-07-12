<?php

namespace App\Services\Gamification;

use App\Models\User;
use App\Repositories\Contracts\GardenRepositoryInterface;

class GardenService
{
    private GardenRepositoryInterface $gardenRepository;

    public function __construct(GardenRepositoryInterface $gardenRepository)
    {
        $this->gardenRepository = $gardenRepository;
    }

    public function applyDecay(User $user)
    {
        # ambil data kebun user via repository
        $garden = $this->gardenRepository->getGardenByUser($user);

        if (!$garden) return null;

        if (!$garden->last_decay_check) {
            # inisialisasi waktu pemeriksaan decay jika belum pernah dilakukan
            return $this->gardenRepository->updateLastDecayCheck($garden, now());
        }

        # hitung selisih hari sejak pemeriksaan decay terakhir
        $diffInHours = now()->diffInHours($garden->last_decay_check);
        $diffInDays  = (int) ($diffInHours / 24);

        if ($diffInDays > 0) {
            # kurangi HP kebun berdasarkan jumlah hari yang terlewat via repository
            $garden = $this->gardenRepository->updateHp($garden, -$diffInDays * 10);
            
            # majukan penanda waktu pemeriksaan decay berikutnya berdasarkan hari yang diproses
            $newCheckDate = \Carbon\Carbon::parse($garden->last_decay_check)->addDays($diffInDays);
            $garden = $this->gardenRepository->updateLastDecayCheck($garden, $newCheckDate);
        }

        return $garden;
    }

    public function addHp(User $user, string $difficulty): void
    {
        # dapatkan detail kebun user via repository
        $garden = $this->gardenRepository->getGardenByUser($user);

        if (!$garden) return;

        # tentukan jumlah pemulihan HP berdasarkan kesulitan tugas
        $hpMapping = ['easy' => 5, 'medium' => 10, 'hard' => 20];
        # tambahkan HP tanaman kebun via repository
        $this->gardenRepository->updateHp($garden, $hpMapping[$difficulty] ?? 5);
    }

    public function syncPlantStage(User $user): void
    {
        # ambil data kebun user via repository
        $garden = $this->gardenRepository->getGardenByUser($user);

        if (!$garden) return;

        # tentukan tingkatan fase tanaman baru berdasarkan level user
        $newStage    = $this->resolveStage($user->level);
        $stageOrder  = ['seed' => 0, 'sprout' => 1, 'tree' => 2];
        $currentOrder = $stageOrder[$garden->plant_stage] ?? 0;
        $newOrder     = $stageOrder[$newStage] ?? 0;

        if ($newOrder > $currentOrder) {
            # perbarui fase tumbuh tanaman ke tahap berikutnya via repository
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
