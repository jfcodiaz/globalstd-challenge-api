<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreloadUserRoles
{
    public function handle(Request $request, Closure $next)
    {
        if ($user = $request->user()) {
            $user->loadMissing('roles');
        }

        return $next($request);
    }
}
