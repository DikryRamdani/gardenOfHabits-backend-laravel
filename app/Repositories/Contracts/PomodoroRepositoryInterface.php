<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use App\Models\PomodoroSessions;

interface PomodoroRepositoryInterface
{
    public function getActiveSession(User $user): ?PomodoroSessions;
    public function create(User $user): PomodoroSessions;
    public function finish(PomodoroSessions $session): PomodoroSessions;
}
