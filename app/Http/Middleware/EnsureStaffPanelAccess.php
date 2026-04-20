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

    $slug = $user->role?->slug;
    // Ongeza Role::MANAGER_SLUG hapa
    if (in_array($slug, [Role::RECEPTION_SLUG, Role::DIRECTOR_SLUG, Role::SUPER_ADMIN_SLUG, Role::MANAGER_SLUG], true)) {
        return $next($request);
    }

    abort(403);
}
}
