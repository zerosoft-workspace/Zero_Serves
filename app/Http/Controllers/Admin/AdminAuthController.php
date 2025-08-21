<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    // Login formunu göster
    public function showLoginForm()
    {
        if (Auth::check() && auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login'); // resources/views/admin/auth/login.blade.php
    }

    // Giriş işlemi
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt(array_merge($credentials, ['role' => 'admin']), $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Bilgiler yanlış veya admin yetkiniz yok.',
        ])->onlyInput('email');
    }

    // Çıkış işlemi
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
