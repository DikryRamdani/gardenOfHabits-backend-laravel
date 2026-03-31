<?php

namespace App\Services\PomodoroSession;

use App\Models\User;
use App\Repositories\PomodoroRepository;
use App\Services\Gamification\StreakService;

class PomodoroService
{
    private PomodoroRepository $pomodoroRepository;
    private StreakService $streakService;

    public function __construct(PomodoroRepository $pomodoroRepository, StreakService $streakService)
    {
        $this->pomodoroRepository = $pomodoroRepository;
        $this->streakService      = $streakService;
    }

    /**
     * Start a new pomodoro session.
     * Returns the session on success, or ['error' => 'ALREADY_ACTIVE', 'session' => ...] if conflict.
     */
    public function start(User $user): array
    {
        $session = $this->pomodoroRepository->getActiveSession($user);

        if ($session) {
            $error = $session->status === 'cooldown' ? 'IN_COOLDOWN' : 'ALREADY_ACTIVE';
            return ['error' => $error, 'session' => $session];
        }

        return ['session' => $this->pomodoroRepository->create($user)];
    }

    /**
     * Finish an active pomodoro session.
     * Returns data on success, or ['error' => 'NOT_FOUND'] / ['error' => 'TOO_EARLY', 'remaining' => int].
     */
    public function finish(User $user): array
    {
        $session = $this->pomodoroRepository->getActiveSession($user);

        if (!$session) {
            return ['error' => 'NOT_FOUND'];
        }

        $minutesElapsed = $session->created_at->diffInMinutes(now());

        if ($minutesElapsed < 25) {
            return ['error' => 'TOO_EARLY', 'remaining' => 25 - $minutesElapsed];
        }

        $session    = $this->pomodoroRepository->finish($session);
        $streakInfo = $this->streakService->updateStreak($user);

        return [
            'session'     => $session,
            'streak_info' => $streakInfo,
        ];
    }

    /**
     * Get current active session status.
     */
    public function status(User $user): array
    {
        return ['session' => $this->pomodoroRepository->getActiveSession($user)];
    }
}
