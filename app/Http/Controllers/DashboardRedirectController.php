<?php

namespace App\Http\Controllers;

use App\Enums\Role;

class DashboardRedirectController extends Controller
{
    public function __invoke()
    {
        $role = auth()->user()->role;

        return match ($role) {
            Role::ADMIN, Role::OPERATOR => redirect()->route('admin.dashboard'),
            Role::PEMBINA => redirect()->route('pembina.dashboard'),
            Role::KEPALA_SEKOLAH => redirect()->route('admin.laporan.index'),
            default => redirect()->route('siswa.daftar'),
        };
    }
}
