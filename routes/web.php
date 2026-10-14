<?php

use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublikController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/eskul', [PublikController::class, 'eskul'])->name('publik.eskul');
Route::get('/jadwal', [PublikController::class, 'jadwal'])->name('publik.jadwal');

Route::get('/dashboard', DashboardRedirectController::class)->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/siswa.php';
require __DIR__.'/admin.php';
require __DIR__.'/admin-lanjutan.php';
require __DIR__.'/pembina.php';
