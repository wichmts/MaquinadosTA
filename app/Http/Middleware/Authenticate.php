<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;

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

    // public function handle($request, Closure $next)
    // {
    //     if ($this->auth->guest())
    //         return redirect()->guest('login');
        
    //     $user = $request->user();
        
    //     if (!$user->active)
    //         abort(403, 'Tu cuenta ha sido suspendida.');

    //     return $next($request);
    // }


}
