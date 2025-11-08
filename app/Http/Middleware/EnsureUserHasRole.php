<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        if (! $user) {
            throw new AccessDeniedHttpException();
        }

        $allowedRoles = collect($roles)
            ->filter()
            ->map(fn ($role) => UserRole::from($role));

        if ($allowedRoles->isEmpty()) {
            return $next($request);
        }

        if (! $allowedRoles->contains(fn (UserRole $role) => $user->hasRole($role))) {
            throw new AccessDeniedHttpException();
        }

        return $next($request);
    }
}
