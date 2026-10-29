<?php

use App\Http\Controllers\Pembina\DashboardController;
use App\Http\Controllers\Pembina\EskulSayaController;
use App\Http\Controllers\Pembina\IzinController;
use App\Http\Controllers\Pembina\JadwalController;
use App\Http\Controllers\Pembina\KegiatanController;
use App\Http\Controllers\Pembina\KehadiranController;
use App\Http\Controllers\Pembina\NilaiController;
use App\Http\Controllers\Pembina\PertemuanController;
use App\Http\Controllers\Pembina\ScanController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:pembina', \App\Http\Middleware\MaintenanceMode::class])->prefix('pembina')->name('pembina.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('eskul', [EskulSayaController::class, 'index'])->name('eskul.index');
    Route::get('eskul/{eskul}', [EskulSayaController::class, 'show'])->name('eskul.show');
    Route::get('eskul/{eskul}/export', [EskulSayaController::class, 'export'])->name('eskul.export');
    Route::post('eskul/{eskul}/anggota', [EskulSayaController::class, 'tambahAnggota'])->name('anggota.store');
    Route::delete('eskul/{eskul}/anggota/{registration}', [EskulSayaController::class, 'hapusAnggota'])->name('anggota.destroy');

    Route::post('eskul/{eskul}/jadwals', [JadwalController::class, 'store'])->name('jadwals.store');
    Route::put('jadwals/{jadwal}', [JadwalController::class, 'update'])->name('jadwals.update');
    Route::delete('jadwals/{jadwal}', [JadwalController::class, 'destroy'])->name('jadwals.destroy');

    Route::post('eskul/{eskul}/kehadiran', [KehadiranController::class, 'store'])->name('kehadiran.store');
    Route::post('eskul/{eskul}/kehadiran/lock', [KehadiranController::class, 'lock'])->name('kehadiran.lock');
    Route::get('eskul/{eskul}/scan', [ScanController::class, 'show'])->name('scan.show');
    Route::post('eskul/{eskul}/scan', [ScanController::class, 'store'])->name('scan.store');
    Route::post('eskul/{eskul}/pertemuan', [PertemuanController::class, 'store'])->name('pertemuan.store');
    Route::delete('pertemuan/{pertemuan}', [PertemuanController::class, 'destroy'])->name('pertemuan.destroy');
    Route::post('eskul/{eskul}/nilai', [NilaiController::class, 'store'])->name('nilai.store');

    Route::post('izin/{izin}/approve', [IzinController::class, 'approve'])->name('izin.approve');
    Route::post('izin/{izin}/reject', [IzinController::class, 'reject'])->name('izin.reject');

    Route::resource('kegiatan', KegiatanController::class);
});
