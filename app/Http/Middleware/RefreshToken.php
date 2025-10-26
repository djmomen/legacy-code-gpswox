<?php namespace App\Http\Middleware;

use Closure;
use App;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RefreshToken {

    const TOKEN = "3541db5f0ce7e1b655beca75bcb26766";

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        if ($response instanceof BinaryFileResponse)
            return $response;

        if ($response instanceof StreamedResponse)
            return $response;

        $response->header('X-Refresh-Token', self::TOKEN);

        return $response;
    }

}
