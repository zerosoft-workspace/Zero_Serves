<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CSRFController extends Controller
{
    /**
     * CSRF token'ı yenile ve döndür
     */
    public function refreshToken(Request $request)
    {
        $request->session()->regenerateToken();
        
        return response()->json([
            'token' => csrf_token(),
            'message' => 'CSRF token yenilendi'
        ]);
    }
}
