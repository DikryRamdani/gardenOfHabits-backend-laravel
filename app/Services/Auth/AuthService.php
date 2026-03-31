<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use App\Repositories\RefreshTokenRepository;

class AuthService
{
    private UserRepository $userRepository;
    private RefreshTokenRepository $refreshTokenRepository;

    public function __construct(UserRepository $userRepository, RefreshTokenRepository $refreshTokenRepository)
    {
        $this->userRepository = $userRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    public function Login(array $data): ?array
    {
        $user = $this->userRepository->findByUsername($data['username']);

        if (!$user) {
            return null;
        }

        $ok = Auth::attempt([
            'email'    => $user->email,
            'password' => $data['password'],
        ]);

        if (!$ok) {
            return null;
        }

        // Use the $user object directly instead of Auth::user() 
        // which might return incorrect user if session conflict occurs
        $accessToken  = $user->createToken('access_token')->plainTextToken;
        $refreshToken = $this->refreshTokenRepository->create($user);

        return [
            'user'          => $user,
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken->token,
        ];
    }

    public function Register(array $data): ?array
    {
        if($this->userRepository->findByUsername($data['username'])) {
            return null;
        }
        
        $user = $this->userRepository->create($data);
        $this->userRepository->createGardenForUser($user, $data);
        $accessToken = $user->createToken('access_token')->plainTextToken;
        $refreshToken = $this->refreshTokenRepository->create($user);

        return [
            'user'          => $user->fresh('garden'),
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken->token,
        ];
    }

    public function Logout(Request $request): void
    {
        $user  = $request->user();
        $token = $user->currentAccessToken();

        if ($token) {
            $user->tokens()->where('id', $token->id)->delete();
        }

        $this->refreshTokenRepository->deleteByUser($user);
    }

    public function Refresh(string $refreshToken): ?array
    {
        $record = $this->refreshTokenRepository->findValid($refreshToken);

        if (!$record) {
            return null;
        }

        $user = $record->user;
        $user->tokens()->delete();

        $accessToken     = $user->createToken('access_token')->plainTextToken;
        $newRefreshToken = $this->refreshTokenRepository->create($user);

        return [
            'access_token'  => $accessToken,
            'refresh_token' => $newRefreshToken->token,
        ];
    }
}