<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;

class Authenticate
{
    public function handle($request, Closure $next)
    {
        $apiKey = $request->header('Authorization');
        if ($apiKey) {
            $apiKeys = config('api_key');
            if (in_array($apiKey, $apiKeys)) {
                return $next($request);
            }
        }
        throw new AuthorizationException();
    }
}
