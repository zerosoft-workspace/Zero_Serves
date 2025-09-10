<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Nereye yönlendirileceğini belirler.
     * JSON bekleyen (API/Ajax) isteklerde null döner ve 401 atılır.
     */
    protected function redirectTo($request): ?string
    {
        // API/Ajax ise 401 JSON verilsin, redirect olmasın
        if ($request->expectsJson()) {
            return null;
        }

        // Admin alanı
        if ($request->is('admin') || $request->is('admin/*')) {
            return route('admin.login');
        }

        // Garson alanı
        if ($request->is('waiter') || $request->is('waiter/*')) {
            return route('waiter.login');
        }

        // Diğer her şey: genel login alias'ı
        return route('login'); // routes/web.php'de eklediğimiz alias
    }
}
