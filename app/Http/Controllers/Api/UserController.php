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
        
        # periksa dan reset status streak harian user via service
        $user = $this->streakService->checkAndResetStreak($user);

        return response()->json([
            'message' => 'Profile retrieved successfully',
            'data'    => $user
        ]);
    }

    public function updatePassword(Request $request)
    {
        # validasi input password lama dan password baru
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|different:current_password',
        ]);

        $user = User::findOrFail($request->user()->id);

        # pastikan kesesuaian input password lama dengan password terdaftar
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'The provided current password does not match.'
            ], 422);
        }

        # enkripsi dan perbarui password baru user di database
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully.'
        ]);
    }

    public function updateAvatar(Request $request)
    {
        # validasi berkas gambar foto profil baru
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $user = $request->user();

        # hapus berkas gambar foto profil lama dari disk penyimpanan
        if ($user->profile_picture) {
            Storage::disk('public')->delete(str_replace('storage/', '', $user->profile_picture));
        }

        # simpan berkas gambar foto profil terupdate ke disk publik
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
