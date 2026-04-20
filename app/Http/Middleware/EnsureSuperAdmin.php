<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{public function handle(Request $request, Closure $next): Response
{
    $user = $request->user();

    // Badilisha hapa ili uruhusu Super Admin na Manager
    if (! $user || (! $user->isSuperAdmin() && ! $user->isManager())) {
        abort(403);
    }

    return $next($request);
}
}
