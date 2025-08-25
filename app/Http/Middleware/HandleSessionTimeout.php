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
        // Sadece girişli kullanıcılar için çalışsın
        if (!Auth::check()) {
            return $next($request);
        }

        // Config: dakika -> saniye
        $lifetime = (int) config('session.lifetime', 120) * 60;

        // Şu anki zaman (timestamp)
        $now = time();

        // İlk kez geliyorsa set et
        $last = (int) $request->session()->get('last_activity', $now);

        // Timeout kontrolü
        if (($now - $last) > $lifetime) {
            Log::warning('Session timed out', [
                'user_id' => Auth::id(),
                'email' => Auth::user()->email ?? null,
                'guard' => config('auth.defaults.guard'),
            ]);

            // Oturumu kapat + tamamen sıfırla
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Hangi login sayfasına döneceğiz? (prefix/route adına bak)
            $routeName = $request->route()?->getName() ?? '';
            $uri = $request->path(); // örn: admin/..., waiter/...

            if (str_starts_with($routeName, 'waiter.') || str_starts_with($uri, 'waiter')) {
                return redirect()->route('waiter.login')->withErrors([
                    'session' => 'Oturumunuz zaman aşımına uğradı. Lütfen tekrar giriş yapın.',
                ]);
            }

            // Varsayılan: admin login; yoksa landing
            if (app('router')->has('admin.login')) {
                return redirect()->route('admin.login')->withErrors([
                    'session' => 'Oturumunuz zaman aşımına uğradı. Lütfen tekrar giriş yapın.',
                ]);
            }

            return redirect()->route('landing')->with('message', 'Oturumunuz zaman aşımına uğradı.');
        }

        // Aktiviteyi güncelle (integer timestamp)
        $request->session()->put('last_activity', $now);

        return $next($request);
    }
}
