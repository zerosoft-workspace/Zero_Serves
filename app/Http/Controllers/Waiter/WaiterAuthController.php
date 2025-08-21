<?php

namespace App\Http\Controllers\Waiter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaiterAuthController extends Controller
{
    /**
     * Giriş formunu göster
     */
    public function showLoginForm()
    {
        return view('waiter.auth.login');
    }

    /**
     * Giriş işlemi
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:190'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Kullanıcı rolü waiter değilse izin verme
            if (auth()->user()->role !== 'waiter') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Bu hesap garson yetkisine sahip değil.',
                ])->onlyInput('email');
            }

            return redirect()->route('waiter.dashboard');
        }

        return back()->withErrors([
            'email' => 'E-posta veya şifre hatalı.',
        ])->onlyInput('email');
    }

    /**
     * Çıkış işlemi
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return view('landing');
    }
}
