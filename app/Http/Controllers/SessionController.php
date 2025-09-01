<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SessionController extends Controller
{
    /**
     * CSRF token'ı döndür
     */
    public function getCsrfToken()
    {
        return response()->json([
            'token' => csrf_token()
        ]);
    }

    /**
     * Oturum durumunu kontrol et
     */
    public function checkStatus()
    {
        if (Auth::check()) {
            return response()->json([
                'authenticated' => true,
                'user' => Auth::user()->only(['id', 'name', 'role'])
            ]);
        }

        return response()->json([
            'authenticated' => false
        ], 401);
    }

    /**
     * Heartbeat - kullanıcının aktif olduğunu belirtir
     */
    public function heartbeat(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = Auth::user();
        
        // Son aktivite zamanını güncelle
        DB::table('users')
            ->where('id', $user->id)
            ->update(['last_activity' => Carbon::now()]);

        // Session'da da güncelle
        session(['last_activity' => Carbon::now()->timestamp]);

        return response()->json([
            'success' => true,
            'timestamp' => Carbon::now()->timestamp
        ]);
    }

    /**
     * Tarayıcı kapanması durumunda oturum sonlandır
     */
    public function browserClose(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Kullanıcıyı offline yap
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'last_activity' => Carbon::now()->subMinutes(5), // 5 dakika öncesi
                    'is_online' => false
                ]);

            // Oturumu sonlandır
            Auth::logout();
            session()->flush();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Oturum süresi dolmuş sayfası
     */
    public function expired()
    {
        // Tüm guard'lardan çıkış yap ve session'ı tamamen temizle
        $guards = ['web', 'admin', 'waiter', 'customer'];
        
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::guard($guard)->logout();
            }
        }
        
        // Session'ı tamamen temizle
        session()->flush();
        session()->regenerate();

        return view('auth.session-expired');
    }

    /**
     * Manuel oturum sonlandırma
     */
    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Kullanıcıyı offline yap
            DB::table('users')
                ->where('id', $user->id)
                ->update(['is_online' => false]);
        }

        Auth::logout();
        session()->flush();
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('landing')
            ]);
        }

        return redirect()->route('landing');
    }
}
