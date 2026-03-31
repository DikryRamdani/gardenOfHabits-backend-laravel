<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\PomodoroSessions;

class PomodoroRepository
{
    public function getActiveSession(User $user): ?PomodoroSessions
    {
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
        return PomodoroSessions::create([
            'user_id'          => $user->id,
            'duration_minutes' => 25,
            'status'           => 'active',
            'created_at'       => now(),
        ]);
    }

    public function finish(PomodoroSessions $session): PomodoroSessions
    {
        $session->update([
            'status'         => 'cooldown',
            'cooldown_until' => now()->addMinutes(5),
        ]);

        return $session->fresh();
    }
}
