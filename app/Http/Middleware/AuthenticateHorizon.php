<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;

class AuthenticateHorizon
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        \Horizon::auth(function ($request) use ($guard) {
            return Auth::guard($guard)->check();
        });

        return $next($request);
    }
}
