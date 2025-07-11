<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class EnsureTokenCan
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $can)
    {
        if (! $request->user()->tokenCan($can)) {
            throw new AuthenticationException(
                'Unauthenticated.'
            );
        }

        return $next($request);
    }
}
