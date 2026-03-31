<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\GardenController;
use App\Http\Controllers\Api\PomodoroController;

use App\Http\Controllers\Api\UserController;

Route::post('/login', [AuthController::class, 'Login']);
Route::post('/register', [AuthController::class, 'Register']);
Route::post('/refresh', [AuthController::class, 'Refresh']);
Route::post('/logout', [AuthController::class, 'Logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user/settings/password', [UserController::class, 'updatePassword']);
    Route::post('/user/settings/avatar', [UserController::class, 'updateAvatar']);
    
    Route::get('/user', [UserController::class, 'getProfile']);

    Route::patch('/user', function (Request $request) {
        $user = $request->user();

        $validated = $request->validate([
            'username' => 'sometimes|required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'sometimes|required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|nullable|string|min:6',
            'profile_picture' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validated['profile_picture'] = Storage::url($path);
        }

        if (array_key_exists('password', $validated) && !$validated['password']) {
            unset($validated['password']);
        }

        $user->fill($validated);
        $user->save();

        return response()->json($user, 200);
    });

    Route::get('/garden', [GardenController::class, 'show']);

    Route::apiResource('tasks', TaskController::class);
    Route::patch('/tasks/{id}/complete', [TaskController::class, 'complete']);

    Route::post('/pomodoro/start', [PomodoroController::class, 'start']);
    Route::post('/pomodoro/finish', [PomodoroController::class, 'finish']);
    Route::get('/pomodoro/status', [PomodoroController::class, 'status']);
});
