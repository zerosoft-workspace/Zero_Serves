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
        // Sadece aktivite takibi yap, timeout kontrolü yapma
        // HandleSessionTimeout middleware'i timeout'u hallediyor
        
        $guards = ['web', 'admin', 'waiter', 'customer'];
        
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // Son aktivite zamanını güncelle
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['last_activity' => Carbon::now()]);
                
                // Session'da da son aktivite zamanını sakla
                session(['last_activity' => time()]);
            }
        }

        return $next($request);
    }
}
