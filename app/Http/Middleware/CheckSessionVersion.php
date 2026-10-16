<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menghanguskan sesi lama saat session_version user berubah
 * (mis. setelah ganti/reset password).
 */
class CheckSessionVersion
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $request->session()->has('session_version')) {
            // Sesi lama tanpa cap versi (mis. dari alur register lawas): cap sekarang
            // agar tidak lolos selamanya, lalu lanjut.
            $request->session()->put('session_version', $user->getAttribute('session_version'));
        }

        if (
            $user
            && $request->session()->has('session_version')
            && (int) $request->session()->get('session_version') !== (int) $user->getAttribute('session_version')
        ) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $loginRoute = $user && in_array($user->role, [
                \App\Enums\Role::ADMIN,
                \App\Enums\Role::OPERATOR,
                \App\Enums\Role::PEMBINA,
                \App\Enums\Role::KEPALA_SEKOLAH,
                \App\Enums\Role::WALI_KELAS,
            ], true) ? 'admin.login' : 'login';

            return redirect()->route($loginRoute)->withErrors([
                'email' => 'Sesi Anda telah berakhir. Silakan login kembali.',
            ]);
        }

        $response = $next($request);

        if ($user) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }

        return $response;
    }
}
