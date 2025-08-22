<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use function Laravel\Prompts\warning;

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
        Log::info('Form Submission CSRF', [
            'token' => $request->session()->token(),
            'request_token' => $request->_token,
            'form' => 'form_adı'
        ]);
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt(array_merge($credentials, ['role' => 'admin']), $request->boolean('remember'))) {
            $request->session()->regenerate();
            Log::info('Login successful', ['email' => $request->email]);
            return redirect()->intended(route('admin.dashboard'));
        }

        Log::warning('Login failed', ['email' => $request->email]);
        return back()->withErrors([
            'email' => 'Bilgiler yanlış veya admin yetkiniz yok.',
        ])->onlyInput('email');
    }

    // Çıkış işlemi
    public function logout(Request $request)
    {
        Log::info('Logout attempt', ['email' => Auth::check() ? Auth::user()->email : 'guest']);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Log::info('CSRF Token after regenerate', ['token' => $request->session()->token()]);

        return redirect()->route('admin.login')->with('message', 'Başarıyla çıkış yaptınız.');
    }
}