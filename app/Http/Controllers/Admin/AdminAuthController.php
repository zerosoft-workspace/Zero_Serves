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
        if (Auth::guard('admin')->check()) {
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
        $attempt = Auth::guard('admin')->attempt(
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
            'email' => Auth::guard('admin')->check() ? Auth::guard('admin')->user()->email : 'guest',
            'ip' => $request->ip(),
            'method' => $request->method(),
        ]);

        // GET isteği için CSRF kontrolü yapmayalım
        if ($request->isMethod('GET')) {
            Auth::guard('admin')->logout();
            
            // Çoklu oturum sistemi için sadece admin guard'ını temizle
            // Session'ı tamamen invalidate etme - diğer guard'lar etkilenmesin
            $request->session()->forget('login_admin_59ba36addc2b2f9401580f014c7f58ea4e30989d');
            $request->session()->regenerateToken();
            
            return redirect()
                ->route('admin.login')
                ->with('message', 'Başarıyla çıkış yaptınız.');
        }

        // POST isteği için normal CSRF koruması devam eder
        Auth::guard('admin')->logout();

        // Çoklu oturum sistemi için sadece admin guard'ını temizle
        $request->session()->forget('login_admin_59ba36addc2b2f9401580f014c7f58ea4e30989d');
        $request->session()->regenerateToken();

        if (config('app.debug')) {
            Log::info('CSRF Token after regenerate', [
                'token' => $request->session()->token(),
            ]);
        }

        // Tercihen landing'e dön; route yoksa admin.login'e
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
