<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];

    /**
     * Handle CSRF token mismatch
     */
    public function handle($request, \Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (TokenMismatchException $exception) {
            // AJAX istekleri için JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Oturumunuzun süresi dolmuş. Lütfen sayfayı yenileyin.',
                    'error' => 'csrf_token_mismatch',
                    'redirect' => request()->url()
                ], 419);
            }

            // Normal form istekleri için redirect with message
            return redirect()->back()
                ->withInput($request->except('_token'))
                ->withErrors(['csrf' => 'Oturumunuzun süresi dolmuş. Lütfen tekrar deneyin.']);
        }
    }
}
