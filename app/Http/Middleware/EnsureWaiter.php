<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureWaiter
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->role !== 'waiter') {
            return redirect()->route('waiter.login'); // abort(403) yerine
        }
        return $next($request);
    }
}
