<?php

use App\Http\Controllers\Admin\CetakController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EskulController;
use App\Http\Controllers\Admin\EskulJadwalController;
use App\Http\Controllers\Admin\InvitationController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\KenaikanController;
use App\Http\Controllers\Admin\NotifController;
use App\Http\Controllers\Admin\PeriodeController;
use App\Http\Controllers\Admin\RegistrationController;
use App\Http\Controllers\Admin\RekapController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WaBlastController;
use App\Http\Controllers\Admin\WindowController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin,operator,kepala_sekolah', \App\Http\Middleware\BlockKepsekMutations::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('eskuls', EskulController::class);
    Route::post('eskuls/{eskul}/delegasi', [EskulController::class, 'delegasi'])->name('eskuls.delegasi');

    Route::post('eskuls/{eskul}/jadwals', [EskulJadwalController::class, 'store'])->name('jadwals.store');
    Route::put('jadwals/{jadwal}', [EskulJadwalController::class, 'update'])->name('jadwals.update');
    Route::delete('jadwals/{jadwal}', [EskulJadwalController::class, 'destroy'])->name('jadwals.destroy');

    Route::resource('periodes', PeriodeController::class)->parameters(['periodes' => 'periode'])->where(['periode' => '.*']);

    Route::put('periodes/{periode}/window', [WindowController::class, 'update'])->name('windows.update')->where(['periode' => '.*']);
    Route::get('window', [WindowController::class, 'index'])->name('windows.index');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::put('users/{user}/role', [UserController::class, 'updateRole'])->name('users.role');
    Route::put('users/{user}/password', [UserController::class, 'resetPassword'])->name('users.password');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('rekap', [RekapController::class, 'index'])->name('rekap.index');
    Route::get('rekap/{eskul}', [RekapController::class, 'show'])->name('rekap.show');

    Route::get('kelas', [KelasController::class, 'index'])->name('kelas.index');

    Route::get('kenaikan', [KenaikanController::class, 'index'])->name('kenaikan.index');
    Route::post('kenaikan', [KenaikanController::class, 'store'])->name('kenaikan.store');
    Route::post('kenaikan/keluarkan', [KenaikanController::class, 'keluarkanSiswa'])->name('kenaikan.keluarkan');
    Route::get('kenaikan/arsip-keluar', [KenaikanController::class, 'arsipKeluar'])->name('kenaikan.arsip-keluar');

    Route::get('invitations', [InvitationController::class, 'index'])->name('invitations.index');
    Route::post('invitations', [InvitationController::class, 'store'])->name('invitations.store');

    Route::get('notifikasi', [NotifController::class, 'index'])->name('notifikasi.index');
    Route::post('notifikasi/{log}/retry', [NotifController::class, 'retry'])->name('notifikasi.retry');

    Route::get('cetak', [CetakController::class, 'index'])->name('cetak.index');

    Route::post('kelas', [KelasController::class, 'store'])->name('kelas.store');
    Route::post('kelas/sync', [KelasController::class, 'syncFromStudents'])->name('kelas.sync');
    Route::put('kelas/{schoolClass}', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('kelas/{schoolClass}', [KelasController::class, 'destroy'])->name('kelas.destroy');

    Route::get('registrations', [RegistrationController::class, 'index'])->name('registrations.index');
    Route::get('registrations/export', [RegistrationController::class, 'export'])->name('registrations.export');
    Route::get('registrations/{registration}', [RegistrationController::class, 'show'])->name('registrations.show');
    Route::delete('registrations/{registration}', [RegistrationController::class, 'destroy'])->name('registrations.destroy');
    Route::put('registrations/{registration}', [RegistrationController::class, 'update'])->name('registrations.update');
    Route::post('registrations/{registration}/nilai', [RegistrationController::class, 'setNilai'])->name('registrations.nilai');
    Route::post('registrations/{registration}/cabut', [RegistrationController::class, 'cabutKartu'])->name('registrations.cabut');
    Route::post('registrations/{registration}/token', [RegistrationController::class, 'resetToken'])->name('registrations.token');
    Route::post('registrations/{registration}/password', [RegistrationController::class, 'resetPassword'])->name('registrations.password');
    Route::post('registrations/{registration}/sertifikat', [RegistrationController::class, 'terbitkanSertifikat'])->name('registrations.sertifikat');

    Route::post('students/bulk-token', [StudentController::class, 'bulkToken'])->name('students.bulk-token');
    Route::post('students/bulk-delete', [StudentController::class, 'bulkDelete'])->name('students.bulk-delete');
    Route::post('students/wa-blast', [StudentController::class, 'waBlast'])->name('students.wa-blast');
    Route::get('students/status-rombel', [StudentController::class, 'statusRombel'])->name('students.status-rombel');
    Route::get('students/export', [StudentController::class, 'export'])->name('students.export');
    Route::get('students/import', [StudentController::class, 'importForm'])->name('students.import');
    Route::get('students/import/template', [StudentController::class, 'template'])->name('students.import.template');
    Route::post('students/import', [StudentController::class, 'import'])->name('students.import.store');
    Route::resource('students', StudentController::class);
    Route::post('students/{student}/token', [StudentController::class, 'issueToken'])->name('students.token');
    Route::post('students/{student}/revoke', [StudentController::class, 'revokeToken'])->name('students.revoke');

    Route::get('wa-blast', [WaBlastController::class, 'index'])->name('wa-blast.index');
    Route::post('wa-blast/check', [WaBlastController::class, 'checkToken'])->name('wa-blast.check');
    Route::post('wa-blast/save-token', [WaBlastController::class, 'saveToken'])->name('wa-blast.save-token');
    Route::post('wa-blast/disconnect', [WaBlastController::class, 'disconnectToken'])->name('wa-blast.disconnect');
    Route::post('wa-blast/send', [WaBlastController::class, 'send'])->name('wa-blast.send');
});
