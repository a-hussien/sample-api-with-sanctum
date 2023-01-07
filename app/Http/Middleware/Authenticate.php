<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    public function handle($request, Closure $next, ...$guards)
    {
        // Set Authorization header with Bearer key
        if ($request->cookie('Sanctum_Token')) {
            $request->headers->set('Authorization', 'Bearer '.$request->cookie('Sanctum_Token'));
        }

        $this->authenticate($request, $guards);

        return $next($request);
    }
}
