<?php

namespace App\Http\Controllers\Waiter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class WaiterAuthController extends Controller
{

    /** Giriş formu */
    public function showLoginForm()
    {
        if (Auth::guard('waiter')->check()) {
            return redirect()->route('waiter.dashboard');
        }

        return view('waiter.auth.login');
    }

    /** Giriş işlemi */
    public function login(Request $request)
    {
        // Form doğrulama
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:190'],
            'password' => ['required', 'string'],
        ]);

        // Debug modda CSRF ve bağlam logu (isteğe bağlı)
        if (config('app.debug')) {
            Log::info('Waiter login CSRF/debug', [
                'session_token' => $request->session()->token(),
                'request_token' => $request->input('_token'),
                'ip' => $request->ip(),
                'ua' => substr((string) $request->userAgent(), 0, 255),
            ]);
        }

        $remember = $request->boolean('remember');

        // Doğrudan role=waiter ile attempt (daha net ve hızlı)
        $attempt = Auth::guard('waiter')->attempt(
            array_merge($credentials, ['role' => 'waiter']),
            $remember
        );

        if ($attempt) {
            // Session fixation koruması
            $request->session()->regenerate();

            Log::info('Waiter login successful', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
            ]);

            return redirect()->intended(route('waiter.dashboard'));
        }

        Log::warning('Waiter login failed', [
            'email' => $request->input('email'),
            'ip' => $request->ip(),
        ]);

        // Standart Laravel doğrulama hatası
        throw ValidationException::withMessages([
            'email' => __('E‑posta veya şifre hatalı ya da garson yetkiniz yok.'),
        ])->redirectTo(route('waiter.login'));
    }

    /** Çıkış işlemi */
    public function logout(Request $request)
    {
        Log::info('Waiter logout attempt', [
            'email' => Auth::guard('waiter')->check() ? Auth::guard('waiter')->user()->email : 'guest',
            'ip' => $request->ip(),
            'method' => $request->method(),
        ]);

        // GET isteği için CSRF kontrolü yapmayalım
        if ($request->isMethod('GET')) {
            Auth::guard('waiter')->logout();
            
            // Çoklu oturum sistemi için sadece waiter guard'ını temizle
            // Session'ı tamamen invalidate etme - diğer guard'lar etkilenmesin
            $request->session()->forget('login_waiter_59ba36addc2b2f9401580f014c7f58ea4e30989d');
            $request->session()->regenerateToken();
            
            return redirect()
                ->route('waiter.login')
                ->with('message', 'Başarıyla çıkış yaptınız.');
        }

        // POST isteği için normal CSRF koruması devam eder
        Auth::guard('waiter')->logout();

        // Çoklu oturum sistemi için sadece waiter guard'ını temizle
        $request->session()->forget('login_waiter_59ba36addc2b2f9401580f014c7f58ea4e30989d');
        $request->session()->regenerateToken();

        if (config('app.debug')) {
            Log::info('Waiter CSRF token rotated', [
                'token' => $request->session()->token(),
            ]);
        }

        // Logout sonrası her zaman redirect (view değil) — 419/back sorunlarını azaltır
        // Tercihen landing; yoksa waiter.login
        if (app('router')->has('landing')) {
            return redirect()
                ->route('landing')
                ->with('message', __('Başarıyla çıkış yaptınız.'))
                ->withHeaders([
                    'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                    'Pragma' => 'no-cache',
                    'Expires' => 'Sat, 01 Jan 2000 00:00:00 GMT',
                ]);
        }

        // AdminAuthController@logout ve WaiterAuthController@logout
        return redirect()
            ->route('waiter.login') // veya admin.login / waiter.login – tercihine göre
            ->with('message', 'Başarıyla çıkış yaptınız.')
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => 'Sat, 01 Jan 2000 00:00:00 GMT',
            ])
            ->setStatusCode(303); // 👈 See Other (back chain'te 302 kaynaklı gariplikleri azaltır)

    }
}
