<?php

namespace App\Services\Task;

use Illuminate\Support\Facades\Auth;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Repositories\Contracts\GardenRepositoryInterface;
use App\Services\Gamification\ExpService;
use App\Services\Gamification\GardenService;

class TaskService
{
    private TaskRepositoryInterface $taskRepository;
    private GardenRepositoryInterface $gardenRepository;
    private ExpService $expService;
    private GardenService $gardenService;

    public function __construct(
        TaskRepositoryInterface $taskRepository,
        GardenRepositoryInterface $gardenRepository,
        ExpService $expService,
        GardenService $gardenService
    ) {
        $this->taskRepository   = $taskRepository;
        $this->gardenRepository = $gardenRepository;
        $this->expService       = $expService;
        $this->gardenService    = $gardenService;
    }

    public function getAllTasks(?string $status = null)
    {
        # ambil seluruh daftar tugas milik user terautentikasi via repository
        return $this->taskRepository->getAllByUser(Auth::user(), $status);
    }

    public function createTask(array $data)
    {
        $user = Auth::user();

        # petakan durasi pengerjaan berdasarkan tingkat kesulitan tugas
        $deadlineMaps     = ['easy' => 1, 'medium' => 3, 'hard' => 7];
        $difficulty       = $data['difficulty'] ?? 'easy';
        $data['deadline'] = now()->addDays($deadlineMaps[$difficulty] ?? 1);

        # buat tugas baru untuk user via repository
        return $this->taskRepository->createForUser($user, $data);
    }

    public function getTaskById($id)
    {
        # cari tugas spesifik milik user berdasarkan id via repository
        return $this->taskRepository->findByUserAndId(Auth::user(), $id);
    }

    public function updateTask(array $data, $id)
    {
        $user = Auth::user();
        # cari tugas terdaftar yang akan diperbarui via repository
        $task = $this->taskRepository->findByUserAndId($user, $id);

        if (!$task) return null;

        # simpan perubahan data tugas via repository
        return $this->taskRepository->updateTask($task, $data);
    }

    public function deleteTask($id)
    {
        $user = Auth::user();
        # cari tugas yang akan dihapus berdasarkan id via repository
        $task = $this->taskRepository->findByUserAndId($user, $id);

        if (!$task) return null;

        # hapus tugas dari database via repository
        $this->taskRepository->deleteTask($task);
        return $task;
    }

    public function markTaskAsComplete($id): ?array
    {
        $user   = Auth::user();
        # dapatkan detail tugas yang akan diselesaikan via repository
        $task   = $this->taskRepository->findByUserAndId($user, $id);
        # ambil status kebun saat ini via repository
        $garden = $this->gardenRepository->getGardenByUser($user);

        if (!$task || !$garden || $task->is_completed) return null;

        # tandai tugas sebagai selesai di database via repository
        $task      = $this->taskRepository->markCompleted($task);
        # tambahkan exp user sebagai hadiah pengerjaan tugas via service gamifikasi
        $rewardExp = $this->expService->addExp($user, $task->difficulty);

        # pulihkan HP tanaman di kebun berdasarkan kesulitan tugas via service kebun
        $this->gardenService->addHp($user, $task->difficulty);
        $user->refresh();
        # sinkronisasikan fase tumbuh tanaman berdasarkan level baru user
        $this->gardenService->syncPlantStage($user);

        # dapatkan status kebun terupdate via repository
        $garden = $this->gardenRepository->getGardenByUser($user);

        return [
            'task'       => $task,
            'garden'     => $garden,
            'reward_exp' => $rewardExp,
        ];
    }
}