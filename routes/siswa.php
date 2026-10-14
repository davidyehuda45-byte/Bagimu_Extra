<?php

use App\Http\Controllers\Siswa\DashboardController;
use App\Http\Controllers\Siswa\IzinController;
use App\Http\Controllers\Siswa\KehadiranController;
use App\Http\Controllers\Siswa\NotifikasiController;
use App\Http\Controllers\Siswa\PendaftaranController;
use App\Http\Controllers\Siswa\ProfilController;
use App\Http\Controllers\Siswa\StatusController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:student', \App\Http\Middleware\MaintenanceMode::class])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/', [PendaftaranController::class, 'create'])->name('dashboard');

    Route::get('/daftar', [PendaftaranController::class, 'create'])->name('daftar');
    Route::post('/daftar', [PendaftaranController::class, 'store'])->name('daftar.store');

    Route::get('/status', [StatusController::class, 'index'])->name('status');

    Route::post('/notifikasi/{notifikasi}/baca', [NotifikasiController::class, 'read'])->name('notifikasi.baca');

    Route::get('/kehadiran', [KehadiranController::class, 'index'])->name('kehadiran');

    Route::get('/izin', [IzinController::class, 'index'])->name('izin.index');
    Route::get('/izin/buat', [IzinController::class, 'create'])->name('izin.create');
    Route::post('/izin', [IzinController::class, 'store'])->name('izin.store');

    Route::get('/profil', [ProfilController::class, 'show'])->name('profil');
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
    Route::post('/profil/password', [ProfilController::class, 'password'])->name('profil.password');
});
