<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DisableCache
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Check if the response is not a BinaryFileResponse
        if (!$response instanceof BinaryFileResponse) {
            $response->header('Cache-Control', 'nocache, no-store, max-age=0, must-revalidate');
            $response->header('Pragma', 'no-cache');
            $response->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
        }

        return $response;
    }
}
