<?php

use App\Http\Controllers\Admin\AchievementController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\IzinController;
use App\Http\Controllers\Admin\KehadiranController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\NilaiController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\VerifyController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::middleware(['role:admin,operator,kepala_sekolah', \App\Http\Middleware\BlockKepsekMutations::class, \App\Http\Middleware\MaintenanceMode::class])->group(function () {
        // Kehadiran
        Route::get('kehadiran', [KehadiranController::class, 'index'])->name('kehadiran.index');
        Route::post('kehadiran', [KehadiranController::class, 'store'])->name('kehadiran.store');
        Route::post('kehadiran/lock', [KehadiranController::class, 'lock'])->name('kehadiran.lock');
        Route::post('kehadiran/unlock', [KehadiranController::class, 'unlock'])->name('kehadiran.unlock');

        // Nilai
        Route::get('nilai', [NilaiController::class, 'index'])->name('nilai.index');
        Route::get('nilai/export', [NilaiController::class, 'export'])->name('nilai.export');
        Route::post('nilai', [NilaiController::class, 'store'])->name('nilai.store');

        // Izin
        Route::get('izin', [IzinController::class, 'index'])->name('izin.index');
        Route::post('izin/{izin}/approve', [IzinController::class, 'approve'])->name('izin.approve');
        Route::post('izin/{izin}/reject', [IzinController::class, 'reject'])->name('izin.reject');
    });

    Route::middleware(['role:admin,operator,kepala_sekolah', \App\Http\Middleware\BlockKepsekMutations::class])->group(function () {
        // Sertifikat
        Route::get('certificates', [CertificateController::class, 'index'])->name('certificates.index');
        Route::post('certificates', [CertificateController::class, 'store'])->name('certificates.store');
        Route::get('certificates/{certificate}/cetak', [CertificateController::class, 'cetak'])->name('certificates.cetak');

        // Inventaris
        Route::resource('inventory', InventoryController::class);
        Route::post('inventory/{item}/loans', [LoanController::class, 'store'])->name('loans.store');
        Route::put('loans/{loan}/kembali', [LoanController::class, 'kembali'])->name('loans.kembali');

        // Prestasi
        Route::post('eskuls/{eskul}/achievements', [AchievementController::class, 'store'])->name('achievements.store');
        Route::delete('achievements/{achievement}', [AchievementController::class, 'destroy'])->name('achievements.destroy');
    });

    Route::middleware('role:admin,operator,kepala_sekolah')->group(function () {
        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/export', [LaporanController::class, 'export'])->name('laporan.export');
    });

    Route::middleware('role:admin,kepala_sekolah')->group(function () {
        Route::get('logs', [LogController::class, 'index'])->name('logs.index');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/eligibility', [SettingController::class, 'eligibility'])->name('settings.eligibility');
    Route::post('settings/maintenance', [SettingController::class, 'maintenance'])->name('settings.maintenance');
    Route::post('settings/mfa', [SettingController::class, 'mfaGenerate'])->name('settings.mfa');
    Route::post('settings/mfa/confirm', [SettingController::class, 'mfaConfirm'])->name('settings.mfa.confirm');
    Route::post('settings/mfa/disable', [SettingController::class, 'mfaDisable'])->name('settings.mfa.disable');
    Route::post('settings/fonnte/check', [SettingController::class, 'checkFonnte'])->name('settings.fonnte.check');
    Route::post('settings/fonnte/save', [SettingController::class, 'saveFonnte'])->name('settings.fonnte.save');
    Route::post('settings/fonnte/disconnect', [SettingController::class, 'disconnectFonnte'])->name('settings.fonnte.disconnect');
    });
});

// Verifikasi publik (tanpa auth, tanpa prefix admin)
Route::get('/verify', [VerifyController::class, 'form'])->name('verify.form');
Route::get('/verify/{token}', [VerifyController::class, 'show'])->name('verify.show');
