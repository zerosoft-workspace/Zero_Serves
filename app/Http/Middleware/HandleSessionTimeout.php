<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HandleSessionTimeout
{
    public function handle(Request $request, Closure $next)
    {
        // Herhangi bir guard'da giriş var mı kontrol et
        $guards = ['web', 'admin', 'waiter', 'customer'];
        $loggedInGuard = null;
        $user = null;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $loggedInGuard = $guard;
                $user = Auth::guard($guard)->user();
                break;
            }
        }

        // Hiç giriş yoksa devam et
        if (!$loggedInGuard) {
            return $next($request);
        }

        // Config: dakika -> saniye
        $lifetime = (int) config('session.lifetime', 60) * 60;

        // Şu anki zaman (timestamp)
        $now = time();

        // İlk kez geliyorsa set et
        $last = (int) $request->session()->get('last_activity', $now);

        // Timeout kontrolü
        if (($now - $last) > $lifetime) {
            Log::warning('Session timed out', [
                'user_id' => $user->id,
                'email' => $user->email ?? null,
                'guard' => $loggedInGuard,
            ]);

            // Sadece ilgili guard'dan çıkış yap
            Auth::guard($loggedInGuard)->logout();
            
            // Çoklu oturum sistemi için sadece ilgili guard'ın session key'ini sil
            $sessionKey = 'login_' . $loggedInGuard . '_59ba36addc2b2f9401580f014c7f58ea4e30989d';
            $request->session()->forget($sessionKey);
            $request->session()->regenerateToken();

            // Hangi login sayfasına döneceğiz?
            $routeName = $request->route()?->getName() ?? '';
            $uri = $request->path();

            if ($loggedInGuard === 'waiter' || str_starts_with($routeName, 'waiter.') || str_starts_with($uri, 'waiter')) {
                return redirect()->route('waiter.login')->withErrors([
                    'session' => 'Oturumunuz zaman aşımına uğradı. Lütfen tekrar giriş yapın.',
                ]);
            }

            if ($loggedInGuard === 'admin' || str_starts_with($routeName, 'admin.') || str_starts_with($uri, 'admin')) {
                return redirect()->route('admin.login')->withErrors([
                    'session' => 'Oturumunuz zaman aşımına uğradı. Lütfen tekrar giriş yapın.',
                ]);
            }

            // Varsayılan: landing sayfası
            return redirect()->route('landing')->with('message', 'Oturumunuz zaman aşımına uğradı.');
        }

        // Aktiviteyi güncelle (integer timestamp)
        $request->session()->put('last_activity', $now);

        return $next($request);
    }
}
