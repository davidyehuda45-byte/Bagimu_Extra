<?php

use App\Http\Controllers\WaliKelas\WaliKelasController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:wali_kelas,admin', \App\Http\Middleware\MaintenanceMode::class])
    ->prefix('wali-kelas')
    ->name('wali-kelas.')
    ->group(function () {
        Route::get('/', [WaliKelasController::class, 'dashboard'])->name('dashboard');
        Route::get('siswa', [WaliKelasController::class, 'siswa'])->name('siswa');
        Route::post('siswa/masukkan', [WaliKelasController::class, 'masukkanSiswa'])->name('siswa.masukkan');
        Route::post('siswa/keluarkan', [WaliKelasController::class, 'keluarkanSiswa'])->name('siswa.keluarkan');
        Route::post('siswa/keluarkan-massal', [WaliKelasController::class, 'bulkKeluarkanSiswa'])->name('siswa.keluarkan-massal');
        Route::get('nilai', [WaliKelasController::class, 'nilai'])->name('nilai');
        Route::get('nilai/cetak', [WaliKelasController::class, 'cetakNilai'])->name('cetak-nilai');
    });

