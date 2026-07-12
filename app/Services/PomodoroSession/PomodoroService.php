<?php

namespace App\Services\PomodoroSession;

use App\Models\User;
use App\Repositories\Contracts\PomodoroRepositoryInterface;
use App\Services\Gamification\StreakService;

class PomodoroService
{
    private PomodoroRepositoryInterface $pomodoroRepository;
    private StreakService $streakService;

    public function __construct(PomodoroRepositoryInterface $pomodoroRepository, StreakService $streakService)
    {
        $this->pomodoroRepository = $pomodoroRepository;
        $this->streakService      = $streakService;
    }

    public function start(User $user): array
    {
        # periksa apakah user memiliki sesi pomodoro aktif via repository
        $session = $this->pomodoroRepository->getActiveSession($user);

        if ($session) {
            # tentukan tipe konflik (sedang fokus atau sedang masa istirahat)
            $error = $session->status === 'cooldown' ? 'IN_COOLDOWN' : 'ALREADY_ACTIVE';
            return ['error' => $error, 'session' => $session];
        }

        # buat sesi pomodoro baru di database via repository
        return ['session' => $this->pomodoroRepository->create($user)];
    }

    public function finish(User $user): array
    {
        # dapatkan sesi pomodoro aktif milik user via repository
        $session = $this->pomodoroRepository->getActiveSession($user);

        if (!$session) {
            return ['error' => 'NOT_FOUND'];
        }

        # hitung selisih waktu berjalan dalam menit sejak sesi dibuat
        $minutesElapsed = $session->created_at->diffInMinutes(now());

        if ($minutesElapsed < 25) {
            # tolak penyelesaian sesi jika durasi fokus belum mencapai 25 menit
            return ['error' => 'TOO_EARLY', 'remaining' => 25 - $minutesElapsed];
        }

        # tandai sesi pomodoro sebagai selesai via repository
        $session    = $this->pomodoroRepository->finish($session);
        # tambahkan dan perbarui akumulasi streak harian user via service
        $streakInfo = $this->streakService->updateStreak($user);

        return [
            'session'     => $session,
            'streak_info' => $streakInfo,
        ];
    }

    public function status(User $user): array
    {
        # dapatkan status sesi pomodoro aktif saat ini via repository
        return ['session' => $this->pomodoroRepository->getActiveSession($user)];
    }
}
