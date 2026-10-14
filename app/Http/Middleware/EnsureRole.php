<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Contoh: ->middleware('role:admin,pembina').
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        $allowed = array_map(
            fn (string $role) => Role::tryFrom(strtoupper($role)),
            $roles
        );

        if (! $user || ! in_array($user->role, $allowed, true)) {
            abort(403);
        }

        return $next($request);
    }
}
