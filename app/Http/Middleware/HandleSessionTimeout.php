<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HandleSessionTimeout
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->exists('last_activity')) {
            $request->session()->put('last_activity', now());
        }

        $lifetime = config('session.lifetime') * 60; // Dakikayı saniyeye çevir
        $lastActivity = $request->session()->get('last_activity');
        if ($lastActivity && now()->diffInSeconds($lastActivity) > $lifetime) {
            Log::warning('Session timed out', ['email' => $request->email ?? 'guest']);
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login')->withErrors([
                'session' => 'Oturumunuz zaman aşımına uğradı. Lütfen tekrar giriş yapın.',
            ]);
        }

        $request->session()->put('last_activity', now());
        return $next($request);
    }
}