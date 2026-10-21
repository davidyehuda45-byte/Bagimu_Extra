<?php

use App\Http\Controllers\Admin\KehadiranController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\NilaiController;
use App\Http\Controllers\Admin\SettingController;
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
    });
});
