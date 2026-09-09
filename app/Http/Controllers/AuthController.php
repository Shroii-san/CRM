<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = [
            'username' => strtolower(str_replace(' ', '_', $request->username)),
            'password' => $request->password
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $user = Auth::user(); // ambil user yang berhasil login
            
            // cek apakah user aktif
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'loginError' => 'Akun anda tidak aktif. Mohon hubungi admin untuk aktivasi.',
                ])->withInput();
            }
            
            // kalau aktif, lanjut login
            $request->session()->regenerate();
            
            // Toast success message - redirect ke dashboard
            return redirect()->intended('/dashboard')
                ->with('login_success', 'Selamat datang kembali, ' . $user->username . '! 🎉');
        }

        // Login gagal - kembali ke halaman login dengan error
        return back()->withErrors([
            'loginError' => 'Username atau password salah. Silakan coba lagi!',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        \Log::info('Logout triggered');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Toast saat logout - redirect ke login
        return redirect('/')
            ->with('success', 'Anda telah berhasil logout. Sampai jumpa! 👋');
    }
}