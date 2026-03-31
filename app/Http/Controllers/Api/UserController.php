<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Services\Gamification\StreakService;

class UserController extends Controller
{
    protected StreakService $streakService;

    public function __construct(StreakService $streakService)
    {
        $this->streakService = $streakService;
    }

    public function getProfile(Request $request)
    {
        $user = $request->user()->load('garden');
        $user = $this->streakService->checkAndResetStreak($user);

        return response()->json([
            'message' => 'Profile retrieved successfully',
            'data'    => $user
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|different:current_password',
        ]);

        $user = User::findOrFail($request->user()->id);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'The provided current password does not match.'
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully.'
        ]);
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $user = $request->user();

        if ($user->profile_picture) {
            Storage::disk('public')->delete(str_replace('storage/', '', $user->profile_picture));
        }

        $path = $request->file('profile_picture')->store('profiles', 'public');
        $user->profile_picture = 'storage/' . $path;
        $user->save();

        return response()->json([
            'message' => 'Profile picture updated successfully.',
            'profile_picture_url' => asset($user->profile_picture),
            'user' => $user
        ]);
    }
}
