<?php

namespace App\Http\Middleware;

use App\Support\PermissionCatalog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        $permissions = collect(explode(',', $permission))
            ->map(fn (string $slug) => PermissionCatalog::normalizeSlug(trim($slug)))
            ->filter()
            ->values()
            ->all();

        if ($permissions === []) {
            abort(403);
        }

        if (! $user->isSuperAdmin() && ! $user->hasAnyPermission($permissions)) {
            abort(403);
        }

        return $next($request);
    }
}
