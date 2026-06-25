<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;

class AuthService
{
    public function login(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            throw new Exception('Kredensial tidak valid.', 401);
        }

        $user = User::where('email', $credentials['email'])->firstOrFail();

        // Buat token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function logout(User $user): void
    {
        // Hapus token yang sedang digunakan untuk login
        $user->currentAccessToken()->delete();
    }

    public function refreshToken(User $user)
    {
        $user->currentAccessToken()->delete();
        return $user->createToken('refresh_token')->plainTextToken;
    }
}
