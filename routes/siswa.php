<?php

use App\Http\Controllers\Siswa\PendaftaranController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:student', \App\Http\Middleware\MaintenanceMode::class])
    ->prefix('siswa')
    ->name('siswa.')
    ->group(function () {
        // Alur Siswa yang Disederhanakan:
        // 1. Ketentuan & Aturan (default beranda siswa)
        Route::get('/', [PendaftaranController::class, 'aturan'])->name('dashboard');
        Route::get('/aturan', [PendaftaranController::class, 'aturan'])->name('aturan');

        // 2. Pilih Eskul
        Route::get('/daftar', [PendaftaranController::class, 'create'])->name('daftar');
        Route::post('/daftar', [PendaftaranController::class, 'store'])->name('daftar.store');

        // 3. Konfirmasi Sukses + 5 detik auto-logout
        Route::get('/sukses', [PendaftaranController::class, 'sukses'])->name('sukses');

        // Pengalihan aman untuk rute lama yang dihapus
        Route::redirect('/status', '/siswa/sukses')->name('status');
        Route::redirect('/profil', '/siswa/aturan')->name('profil');
        Route::redirect('/kehadiran', '/siswa/aturan')->name('kehadiran');
        Route::redirect('/izin', '/siswa/aturan')->name('izin');
        Route::redirect('/pengumuman', '/siswa/aturan')->name('pengumuman');
    });
