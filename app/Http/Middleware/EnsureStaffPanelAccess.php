<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffPanelAccess
{
    public function handle(Request $request, Closure $next): Response
{
    $user = $request->user();
    if (! $user) {
        abort(403);
    }

    if ($user->hasStaffPanelAccess() || $user->isManager()) {
        return $next($request);
    }

    abort(403);
}
}
