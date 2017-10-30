<?php

namespace App\Http\Middleware;

use Closure;

class ResponseMiddleware
{
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
        if (
            empty($response->content()) ||
            $response->content() == '[]'
        ) {
            return response()->json(
                ['message' => 'no content'], 202
            );
        }

        return $response;
    }
}
