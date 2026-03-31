<?php

namespace App\Services\Task;

use Illuminate\Support\Facades\Auth;
use App\Repositories\TaskRepository;
use App\Repositories\GardenRepository;
use App\Services\Gamification\ExpService;
use App\Services\Gamification\GardenService;

class TaskService
{
    private TaskRepository $taskRepository;
    private GardenRepository $gardenRepository;
    private ExpService $expService;
    private GardenService $gardenService;

    public function __construct(
        TaskRepository $taskRepository,
        GardenRepository $gardenRepository,
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
        return $this->taskRepository->getAllByUser(Auth::user(), $status);
    }

    public function createTask(array $data)
    {
        $user = Auth::user();

        $deadlineMaps     = ['easy' => 1, 'medium' => 3, 'hard' => 7];
        $difficulty       = $data['difficulty'] ?? 'easy';
        $data['deadline'] = now()->addDays($deadlineMaps[$difficulty] ?? 1);

        return $this->taskRepository->createForUser($user, $data);
    }

    public function getTaskById($id)
    {
        return $this->taskRepository->findByUserAndId(Auth::user(), $id);
    }

    public function updateTask(array $data, $id)
    {
        $user = Auth::user();
        $task = $this->taskRepository->findByUserAndId($user, $id);

        if (!$task) return null;

        return $this->taskRepository->updateTask($task, $data);
    }

    public function deleteTask($id)
    {
        $user = Auth::user();
        $task = $this->taskRepository->findByUserAndId($user, $id);

        if (!$task) return null;

        $this->taskRepository->deleteTask($task);
        return $task;
    }

    public function markTaskAsComplete($id): ?array
    {
        $user   = Auth::user();
        $task   = $this->taskRepository->findByUserAndId($user, $id);
        $garden = $this->gardenRepository->getGardenByUser($user);

        if (!$task || !$garden || $task->is_completed) return null;

        $task      = $this->taskRepository->markCompleted($task);
        $rewardExp = $this->expService->addExp($user, $task->difficulty);

        $this->gardenService->addHp($user, $task->difficulty);
        $user->refresh();
        $this->gardenService->syncPlantStage($user);

        $garden = $this->gardenRepository->getGardenByUser($user);

        return [
            'task'       => $task,
            'garden'     => $garden,
            'reward_exp' => $rewardExp,
        ];
    }
}