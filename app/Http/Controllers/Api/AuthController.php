<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\Auth\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequests;
use App\Http\Requests\LoginRequests;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function Login(LoginRequests $request)
    {
        # jalankan proses autentikasi user via service
        $data = $this->authService->Login($request->validated());

        # tangani kegagalan autentikasi jika kredensial tidak sesuai
        if (!$data) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'data'    => $data,
        ], 200);
    }

    public function Register(RegisterRequests $request)
    {
        # daftarkan user baru via service
        $data = $this->authService->Register($request->validated());

        # kembalikan respon error jika proses registrasi gagal
        if (!$data) {
            return response()->json(['message' => 'Username already taken'], 409);
        }

        return response()->json([
            'message' => 'User registered successfully',
            'data'    => $data,
        ], 201);
    }

    public function Logout(Request $request)
    {
        # bersihkan sesi login user via service
        $this->authService->Logout($request);

        return response()->json(['message' => 'Logout successful'], 200);
    }

    public function Refresh(Request $request)
    {
        # dapatkan token refresh dari request input
        $refreshToken = $request->input('refresh_token');

        # pastikan token refresh dikirimkan di dalam request
        if (!$refreshToken) {
            return response()->json(['message' => 'Refresh token is required'], 422);
        }

        # perbarui masa aktif token akses via service
        $data = $this->authService->Refresh($refreshToken);

        # tangani kegagalan jika token refresh tidak valid atau kedaluwarsa
        if (!$data) {
            return response()->json(['message' => 'Invalid or expired refresh token'], 401);
        }

        return response()->json([
            'message' => 'Token refreshed successfully',
            'data'    => $data,
        ], 200);
    }
}