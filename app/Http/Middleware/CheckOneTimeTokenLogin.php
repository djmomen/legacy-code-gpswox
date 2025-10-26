<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Tobuli\Services\OneTimeTokenService;

class CheckOneTimeTokenLogin
{
    public function handle(Request $request, Closure $next)
    {
        if (!($ott = $request->input('ott')) && !($ott = $request->header('X-OTT'))) {
            return $next($request);
        }

        $limiterKey = 'ott-login:' . $request->ip();

        if (RateLimiter::tooManyAttempts($limiterKey, 5)) {
            return $next($request);
        }

        RateLimiter::hit($limiterKey);

        app(OneTimeTokenService::class)->login($ott);

        return $next($request);
    }
}
