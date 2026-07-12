<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\RefreshTokenRepositoryInterface;

class AuthService
{
    private UserRepositoryInterface $userRepository;
    private RefreshTokenRepositoryInterface $refreshTokenRepository;

    public function __construct(UserRepositoryInterface $userRepository, RefreshTokenRepositoryInterface $refreshTokenRepository)
    {
        $this->userRepository = $userRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    public function Login(array $data): ?array
    {
        # cari user berdasarkan username via repository
        $user = $this->userRepository->findByUsername($data['username']);

        if (!$user) {
            return null;
        }

        # verifikasi kredensial login user
        $ok = Auth::attempt([
            'email'    => $user->email,
            'password' => $data['password'],
        ]);

        if (!$ok) {
            return null;
        }

        # buat token akses baru untuk sesi user
        $accessToken  = $user->createToken('access_token')->plainTextToken;
        # buat token refresh baru via repository
        $refreshToken = $this->refreshTokenRepository->create($user);

        return [
            'user'          => $user,
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken->token,
        ];
    }

    public function Register(array $data): ?array
    {
        # periksa apakah username sudah terdaftar
        if($this->userRepository->findByUsername($data['username'])) {
            return null;
        }
        
        # daftarkan user baru via repository
        $user = $this->userRepository->create($data);
        # inisialisasi kebun (garden) baru untuk user via repository
        $this->userRepository->createGardenForUser($user, $data);
        
        # buat token akses baru untuk pendaftaran berhasil
        $accessToken = $user->createToken('access_token')->plainTextToken;
        # buat token refresh baru via repository
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
        # hapus token akses saat ini untuk keluar sesi
        $token = $user->currentAccessToken();

        if ($token) {
            $user->tokens()->where('id', $token->id)->delete();
        }

        # bersihkan token refresh milik user via repository
        $this->refreshTokenRepository->deleteByUser($user);
    }

    public function Refresh(string $refreshToken): ?array
    {
        # cari token refresh valid yang dikirim user via repository
        $record = $this->refreshTokenRepository->findValid($refreshToken);

        if (!$record) {
            return null;
        }

        $user = $record->user;
        # bersihkan token akses lama user dari database
        $user->tokens()->delete();

        # terbitkan token akses baru
        $accessToken = $user->createToken('access_token')->plainTextToken;
        # buat token refresh terupdate via repository
        $newRefreshToken = $this->refreshTokenRepository->create($user);

        return [
            'access_token'  => $accessToken,
            'refresh_token' => $newRefreshToken->token,
        ];
    }
}