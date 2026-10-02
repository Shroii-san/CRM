<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponse;

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = [
            'name' => strtolower(str_replace(' ', '_', $request->name)),
            'password' => $request->password,
        ];

        if (!Auth::attempt($credentials, $request->filled('remember'))) {
            if ($request->expectsJson()) {
                return $this->errorResponse('Username atau password salah.', 401);
            }
            return back()->withErrors([
                'loginError' => 'username atau password salah. Silakan coba lagi!',
            ])->withInput();
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            if ($request->expectsJson()) {
                return $this->errorResponse('Akun tidak aktif. Hubungi admin untuk aktivasi.', 403);
            }
            return back()->withErrors([
                'loginError' => 'Akun anda tidak aktif. Mohon hubungi admin untuk aktivasi.',
            ])->withInput();
        }

        // Generate Sanctum token untuk API
        $user->tokens()->where('name', 'api-token')->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        // API request - return JSON
        if ($request->expectsJson()) {
            return $this->successResponse(
                data: [
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'role' => $user->role?->name ?? null,
                    ],
                ],
                message: 'Login berhasil.',
                code: 200
            );
        }

        // Web request - return redirect
        $request->session()->regenerate();
        return redirect()->intended('/dashboard')
            ->with('login_success', 'Selamat datang kembali, ' . $user->name . '!')
            ->with('api_token', $token);
    }

    public function logout(Request $request)
    {
        if ($request->expectsJson()) {
            $request->user()?->currentAccessToken()?->delete();
            return $this->successResponse(
                message: 'Logout berhasil.',
                code: 200
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'Anda telah berhasil logout. Sampai jumpa!');
    }
}
