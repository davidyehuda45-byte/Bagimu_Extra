<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Akun Kepala Sekolah hanya untuk membaca: semua request non-GET
 * di area admin ditolak (403).
 */
class BlockKepsekMutations
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === Role::KEPALA_SEKOLAH && ! $request->isMethodSafe()) {
            abort(403, 'Akun Kepala Sekolah hanya untuk membaca.');
        }

        return $next($request);
    }
}
