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
        $data = $this->authService->Login($request->validated());

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
        $data = $this->authService->Register($request->validated());

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
        $this->authService->Logout($request);

        return response()->json(['message' => 'Logout successful'], 200);
    }

    public function Refresh(Request $request)
    {
        $refreshToken = $request->input('refresh_token');

        if (!$refreshToken) {
            return response()->json(['message' => 'Refresh token is required'], 422);
        }

        $data = $this->authService->Refresh($refreshToken);

        if (!$data) {
            return response()->json(['message' => 'Invalid or expired refresh token'], 401);
        }

        return response()->json([
            'message' => 'Token refreshed successfully',
            'data'    => $data,
        ], 200);
    }
}