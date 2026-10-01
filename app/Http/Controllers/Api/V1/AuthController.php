<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Login dan dapatkan API token (Sanctum).
     *
     * POST /api/v1/auth/login
     * Body: { "name": "...", "password": "..." }
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = [
            'name'     => strtolower(str_replace(' ', '_', $request->name)),
            'password' => $request->password,
        ];

        if (!Auth::attempt($credentials)) {
            return $this->errorResponse('Username atau password salah.', 401);
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return $this->errorResponse('Akun tidak aktif. Hubungi admin untuk aktivasi.', 403);
        }

        // Hapus token lama dengan nama yang sama (opsional, untuk single-session)
        $user->tokens()->where('name', 'api-token')->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'user'  => [
                'id'   => $user->id,
                'name' => $user->name,
                'role' => $user->role?->name ?? null,
            ],
        ], 'Login berhasil.');
    }

    /**
     * Logout dan hapus API token saat ini.
     *
     * POST /api/v1/auth/logout
     * Header: Authorization: Bearer {token}
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logout berhasil.');
    }
}
