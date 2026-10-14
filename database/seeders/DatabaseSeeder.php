<?php

namespace Database\Seeders;

use App\Enums\PeriodeStatus;
use App\Enums\Role;
use App\Models\Announcement;
use App\Models\AppSetting;
use App\Models\Eskul;
use App\Models\PendaftaranWindow;
use App\Models\Periode;
use App\Models\Student;
use App\Models\User;
use App\Support\LoginToken;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Pengaman: seeder ini membuat akun demo (password lemah).
        // DILARANG jalan di production kecuali explicitly diizinkan.
        if (app()->environment('production') && env('ALLOW_PROD_SEED') !== 'true') {
            $this->command->error('Seeder demo DIBLOKIR di production (membuat akun password lemah).');
            $this->command->line('Buat admin via: php artisan app:create-admin');
            $this->command->line('Hanya untuk data awal darurat: ALLOW_PROD_SEED=true php artisan db:seed --force');

            return;
        }

        $admin = User::firstOrCreate(
            ['email' => 'admin@sekolah.sch.id'],
            [
                'name' => 'Admin Eskul',
                'password' => Hash::make('password'),
                'role' => Role::ADMIN,
                'username' => 'admin',
            ]
        );

        Periode::firstOrCreate(
            ['periode' => '2026/2027'],
            [
                'nama' => 'Tahun Ajaran 2026/2027',
                'status' => PeriodeStatus::DIBUKA,
                'max_eskul_per_siswa' => 1,
            ]
        );

        PendaftaranWindow::firstOrCreate(
            ['periode' => '2026/2027'],
            [
                'buka' => true,
            ]
        );

        if (! AppSetting::get('school.name')) {
            AppSetting::set('school.name', 'SMK Bagimu Negeriku');
        }
        if (AppSetting::get('school.contact') === null) {
            AppSetting::set('school.contact', '');
        }
        if (! AppSetting::get('school.address')) {
            AppSetting::set('school.address', 'Ngaliyan, Kota Semarang');
        }
        if (! AppSetting::get('school.logo')) {
            AppSetting::set('school.logo', '/logo.png');
        }

        foreach ([
            ['Voli', 'Bola voli'],
            ['Futsal', 'Sepak bola ruangan'],
            ['Kerajinan Tangan', 'Kreativitas dan keterampilan tangan'],
            ['Audio Sound', 'Tata suara dan audio'],
            ['PMR', 'Palang Merah Remaja'],
        ] as [$nama, $deskripsi]) {
            Eskul::firstOrCreate(
                ['name' => $nama],
                [
                    'description' => $deskripsi,
                    'kuota' => 0,
                    'kuota_aktif' => false,
                    'aktif' => true,
                ]
            );
        }

        $futsal = Eskul::where('name', 'Futsal')->first();
        if ($futsal && $futsal->jadwals()->count() === 0) {
            $futsal->jadwals()->createMany([
                ['hari' => 'SELASA', 'jam_mulai' => '15:00', 'jam_selesai' => '17:00', 'tempat' => 'Lapangan Utama'],
                ['hari' => 'JUMAT', 'jam_mulai' => '15:00', 'jam_selesai' => '17:00', 'tempat' => 'Lapangan Utama'],
            ]);
        }

        $voli = Eskul::where('name', 'Voli')->first();
        if ($voli && $voli->jadwals()->count() === 0) {
            $voli->jadwals()->create([
                'hari' => 'RABU', 'jam_mulai' => '15:00', 'jam_selesai' => '17:00', 'tempat' => 'Lapangan Voli',
            ]);
        }

        // Catatan: tabel announcements tidak dipakai aplikasi (tidak ada model);
        // pengumuman dikelola via AppSetting/notifikasi. Jangan seed di sini.

        $siswa = User::firstOrCreate(
            ['email' => 'siswa@sekolah.sch.id'],
            [
                'name' => 'Siswa Demo',
                'password' => Hash::make('password'),
                'role' => Role::STUDENT,
                'username' => 'siswa',
            ]
        );

        $token = $siswa->login_token ?: LoginToken::issue($siswa);

        Student::firstOrCreate(
            ['nis' => '10001'],
            [
                'nama' => 'Siswa Demo',
                'kelas' => 'X RPL 1',
                'jurusan' => 'RPL',
                'gender' => 'LAKI_LAKI',
                'status' => 'AKTIF',
                'user_id' => $siswa->id,
            ]
        );

        // Siswa kedua: data induk ada, akun + token belum (untuk demo alur TU).
        Student::firstOrCreate(
            ['nis' => '10002'],
            [
                'nama' => 'Siswi Demo',
                'kelas' => 'X DKV 1',
                'jurusan' => 'DKV',
                'gender' => 'PEREMPUAN',
                'status' => 'AKTIF',
            ]
        );

        $this->command->info('Admin  : admin@sekolah.sch.id / password');
        $this->command->info('Siswa  : NIS 10001 + token di bawah');
        $this->command->info("Token siswa demo (8 digit): {$token}");
    }
}
