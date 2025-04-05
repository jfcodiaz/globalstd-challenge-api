<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockIfInactiveOrDeleted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active || $user->trashed()) {
            return response()->json([
                'message' => 'User is inactive or has been deleted.',
            ], 403);
        }

        return $next($request);
    }
}
