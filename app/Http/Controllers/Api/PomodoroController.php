<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PomodoroSession\PomodoroService;
use Illuminate\Http\Request;

class PomodoroController extends Controller
{
    protected PomodoroService $pomodoroService;

    public function __construct(PomodoroService $pomodoroService)
    {
        $this->pomodoroService = $pomodoroService;
    }

    public function start(Request $request)
    {
        $result = $this->pomodoroService->start($request->user());

        if (!isset($result['error'])) {
            return response()->json([
                'message' => 'Pomodoro started!, focus for 25 minutes.',
                'data'    => $result['session'],
            ], 201);
        }

        return match ($result['error']) {
            'ALREADY_ACTIVE' => response()->json([
                'message' => 'There is activated session. Finish it first before starting a new one.',
                'data'    => $result['session'],
            ], 409),
            'IN_COOLDOWN' => response()->json([
                'message' => 'Still in cooldown period. Wait a moment before starting a new session.',
                'data'    => $result['session'],
            ], 429),
            default => response()->json(['message' => 'Unknown error'], 500),
        };
    }

    public function finish(Request $request)
    {
        $result = $this->pomodoroService->finish($request->user());

        if (!isset($result['error'])) {
            return response()->json([
                'message' => 'Pomodoro finished! Take a 5 minute break.',
                'data'    => $result,
            ], 200);
        }

        return match ($result['error']) {
            'NOT_FOUND' => response()->json(['message' => 'No active pomodoro session.'], 404),
            'TOO_EARLY' => response()->json([
                'message' => "Session not finished yet. {$result['remaining']} minutes remaining.",
                'data'    => ['remaining_minutes' => $result['remaining']],
            ], 422),
            default => response()->json(['message' => 'Unknown error'], 500),
        };
    }

    public function status(Request $request)
    {
        $result = $this->pomodoroService->status($request->user());

        return response()->json([
            'message' => $result['session'] ? 'Active session found.' : 'No active session.',
            'data'    => $result['session'],
        ], 200);
    }
}
