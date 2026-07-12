<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Models\PomodoroSessions;
use App\Repositories\Contracts\PomodoroRepositoryInterface;

class EloquentPomodoroRepository implements PomodoroRepositoryInterface
{
    public function getActiveSession(User $user): ?PomodoroSessions
    {
        # cari sesi pomodoro aktif atau yang masih dalam masa jeda (cooldown)
        return $user->pomodoroSessions()
            ->whereIn('status', ['active', 'cooldown'])
            ->where(function ($q) {
                $q->where('status', 'active')
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'cooldown')
                         ->where('cooldown_until', '>', now());
                  });
            })
            ->latest('created_at')
            ->first();
    }

    public function create(User $user): PomodoroSessions
    {
        # buat sesi pomodoro baru berstatus aktif selama 25 menit
        return PomodoroSessions::create([
            'user_id'          => $user->id,
            'duration_minutes' => 25,
            'status'           => 'active',
            'created_at'       => now(),
        ]);
    }

    public function finish(PomodoroSessions $session): PomodoroSessions
    {
        # selesaikan sesi dan atur waktu masa jeda istirahat (cooldown) 5 menit
        $session->update([
            'status'         => 'cooldown',
            'cooldown_until' => now()->addMinutes(5),
        ]);

        return $session->fresh();
    }
}
