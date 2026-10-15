<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\MfaController;
use App\Http\Controllers\Auth\SiswaLoginController;
use App\Http\Controllers\Auth\StaffLoginController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Pintu bawaan dialihkan ke login siswa.
    Route::redirect('login', '/siswa/login')->name('login');

    Route::redirect('register', '/siswa/login');

    // Pintu login guru (URL khusus /admin/login): username/email + password.
    Route::get('admin/login', [StaffLoginController::class, 'create'])
        ->name('admin.login');

    Route::post('admin/login', [StaffLoginController::class, 'store'])
        ->middleware('throttle:10,1');

    Route::get('admin/mfa', [MfaController::class, 'create'])
        ->name('admin.mfa');

    Route::post('admin/mfa', [MfaController::class, 'store'])
        ->middleware('throttle:10,1');

    // Pintu siswa: NIS + token 8 digit.
    Route::get('siswa/login', [SiswaLoginController::class, 'create'])
        ->middleware(\App\Http\Middleware\MaintenanceMode::class)
        ->name('siswa.login');

    Route::post('siswa/login', [SiswaLoginController::class, 'store'])
        ->middleware([\App\Http\Middleware\MaintenanceMode::class, 'throttle:10,1']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store'])
        ->middleware('throttle:10,1');

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

Route::match(['get', 'post'], 'auth/session-exit', [AuthenticatedSessionController::class, 'sessionExit'])
    ->name('auth.session.exit');
