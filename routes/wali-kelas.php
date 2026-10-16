<?php

use App\Http\Controllers\WaliKelas\WaliKelasController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:wali_kelas,admin', \App\Http\Middleware\MaintenanceMode::class])
    ->prefix('wali-kelas')
    ->name('wali-kelas.')
    ->group(function () {
        Route::get('/', [WaliKelasController::class, 'dashboard'])->name('dashboard');
        Route::get('siswa', [WaliKelasController::class, 'siswa'])->name('siswa');
        Route::get('nilai', [WaliKelasController::class, 'nilai'])->name('nilai');
        Route::get('nilai/cetak', [WaliKelasController::class, 'cetakNilai'])->name('cetak-nilai');
    });

