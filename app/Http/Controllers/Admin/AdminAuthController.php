<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{

    /** Login formu */
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login'); // resources/views/admin/auth/login.blade.php
    }

    /** Giriş işlemi */
    public function login(Request $request)
    {
        // Form doğrulama
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // (İsteğe bağlı) CSRF debug logu – sadece debug modda
        if (config('app.debug')) {
            Log::info('Form Submission CSRF', [
                'session_token' => $request->session()->token(),
                'request_token' => $request->input('_token'),
                'ip' => $request->ip(),
                'ua' => substr((string) $request->userAgent(), 0, 255),
            ]);
        }

        // Sadece admin rolüne izin ver
        $attempt = Auth::attempt(
            array_merge($credentials, ['role' => 'admin']),
            $request->boolean('remember')
        );

        if ($attempt) {
            // Session fixation koruması
            $request->session()->regenerate();

            Log::info('Login successful', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
            ]);

            return redirect()->intended(route('admin.dashboard'));
        }

        Log::warning('Login failed', [
            'email' => $request->input('email'),
            'ip' => $request->ip(),
        ]);

        // Standart Laravel hatası (errors bag ile geri dön)
        throw ValidationException::withMessages([
            'email' => __('Bilgiler yanlış veya admin yetkiniz yok.'),
        ])->redirectTo(route('admin.login'));
    }

    /** Çıkış işlemi */
    public function logout(Request $request)
    {
        Log::info('Logout attempt', [
            'email' => Auth::check() ? Auth::user()->email : 'guest',
            'ip' => $request->ip(),
        ]);

        Auth::logout();

        // Oturumu tamamen geçersiz kıl + yeni CSRF üret
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if (config('app.debug')) {
            Log::info('CSRF Token after regenerate', [
                'token' => $request->session()->token(),
            ]);
        }

        // Tercihen landing’e dön; route yoksa admin.login’e
        if (app('router')->has('landing')) {
            return redirect()
                ->route('landing')
                ->with('message', __('Başarıyla çıkış yaptınız.'));
        }

        // AdminAuthController@logout ve WaiterAuthController@logout
        return redirect()
            ->route('admin.login') // veya admin.login / waiter.login – tercihine göre
            ->with('message', 'Başarıyla çıkış yaptınız.')
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => 'Sat, 01 Jan 2000 00:00:00 GMT',
            ])
            ->setStatusCode(303); // 👈 See Other (back chain'te 302 kaynaklı gariplikleri azaltır)

    }
}
