<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MultiSessionAuth
{
    /**
     * Handle an incoming request.
     * Çoklu oturum desteği - admin, waiter, customer aynı anda giriş yapabilir
     */
    public function handle(Request $request, Closure $next, $guard = null, ...$roles)
    {
        // Guard belirleme
        $authGuard = $guard ?? 'web';
        
        // Kullanıcı giriş yapmamışsa login sayfasına yönlendir
        if (!Auth::guard($authGuard)->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            
            // Role göre farklı login sayfalarına yönlendir
            $currentPath = $request->path();
            
            if (str_starts_with($currentPath, 'admin')) {
                return redirect()->route('admin.login');
            } elseif (str_starts_with($currentPath, 'waiter')) {
                return redirect()->route('waiter.login');
            } elseif (str_starts_with($currentPath, 'table')) {
                return redirect()->route('customer.login');
            }
            
            return redirect()->route('login');
        }

        $user = Auth::guard($authGuard)->user();
        
        // Rol kontrolü - sadece yetkisiz erişimleri engelle
        if (!empty($roles) && !in_array($user->role, $roles)) {
            // Admin kullanıcısı tüm panellere erişebilir (süper yetki)
            if ($user->role === 'admin') {
                return $next($request);
            }
            
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            
            // Diğer roller için kısıtlama
            switch ($user->role) {
                case 'waiter':
                    return redirect()->route('waiter.dashboard');
                case 'customer':
                    return redirect()->route('customer.dashboard');
                default:
                    return redirect()->route('landing');
            }
        }

        return $next($request);
    }
}
