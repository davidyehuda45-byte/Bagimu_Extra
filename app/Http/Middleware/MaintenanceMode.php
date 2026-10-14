<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use App\Models\AppSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mode pemeliharaan: selama aktif, siswa melihat halaman informasi khusus
 * bahwa sistem sedang dalam perawatan (HTTP 503).
 * Admin, operator, dan pintu login admin tetap dapat mengakses sistem secara normal.
 */
class MaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (AppSetting::get('app.maintenance', '0') === '1') {
            $user = $request->user();

            // Admin, operator, dan kepala sekolah bebas mengakses sistem
            if ($user && in_array($user->role, [Role::ADMIN, Role::OPERATOR, Role::KEPALA_SEKOLAH], true)) {
                return $next($request);
            }

            // Jalur login dan kelola admin tidak diblokir
            if ($request->is('admin/login', 'admin/mfa', 'admin/*')) {
                return $next($request);
            }

            $pesan = AppSetting::get('app.maintenance_msg', 'Sistem sedang dalam proses pemeliharaan dan optimalisasi data.');
            $eta = AppSetting::get('app.maintenance_eta');

            return response()->view('errors.maintenance', compact('pesan', 'eta'), 503);
        }

        return $next($request);
    }
}
