<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SessionActivityTracker
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Tüm guard'ları kontrol et ve aktif olanları güncelle
        $guards = ['web', 'admin', 'waiter', 'customer'];
        
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // Son aktivite zamanını güncelle
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['last_activity' => Carbon::now()]);
                
                // Session'da da son aktivite zamanını sakla
                session(['last_activity' => Carbon::now()->timestamp]);
            }
        }

        // Session süresi kontrolü (config'den dakika cinsinden al, saniyeye çevir)
        $sessionLifetime = config('session.lifetime') * 60; // dakikayı saniyeye çevir
        if (session('last_activity') && (time() - session('last_activity')) > $sessionLifetime) {
            // Tüm guard'lardan çıkış yap
            foreach ($guards as $guard) {
                if (Auth::guard($guard)->check()) {
                    Auth::guard($guard)->logout();
                }
            }
            session()->flush();
            
            // AJAX isteği ise JSON döndür
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Oturum süresi doldu',
                    'redirect' => route('session.expired')
                ], 401);
            }
            
            return redirect()->route('session.expired');
        }

        return $next($request);
    }
}
