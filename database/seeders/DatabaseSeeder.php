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

        $admin = User::create([
            'name' => 'Admin Eskul',
            'email' => 'admin@sekolah.sch.id',
            'password' => Hash::make('password'),
            'role' => Role::ADMIN,
            'username' => 'admin',
        ]);

        Periode::create([
            'periode' => '2026/2027',
            'nama' => 'Tahun Ajaran 2026/2027',
            'status' => PeriodeStatus::DIBUKA,
            'max_eskul_per_siswa' => 1,
        ]);

        PendaftaranWindow::create([
            'periode' => '2026/2027',
            'buka' => true,
        ]);

        AppSetting::set('school.name', 'SMK Bagimu Negeriku');
        AppSetting::set('school.contact', '');
        AppSetting::set('school.address', 'Ngaliyan, Kota Semarang');
        AppSetting::set('school.logo', '/logo.png');

        foreach ([
            ['Voli', 'Bola voli'],
            ['Futsal', 'Sepak bola ruangan'],
            ['Kerajinan Tangan', 'Kreativitas dan keterampilan tangan'],
            ['Audio Sound', 'Tata suara dan audio'],
            ['PMR', 'Palang Merah Remaja'],
        ] as [$nama, $deskripsi]) {
            Eskul::create([
                'name' => $nama,
                'description' => $deskripsi,
                'kuota' => 0,
                'kuota_aktif' => false,
                'aktif' => true,
            ]);
        }

        Eskul::where('name', 'Futsal')->first()?->jadwals()->createMany([
            ['hari' => 'SELASA', 'jam_mulai' => '15:00', 'jam_selesai' => '17:00', 'tempat' => 'Lapangan Utama'],
            ['hari' => 'JUMAT', 'jam_mulai' => '15:00', 'jam_selesai' => '17:00', 'tempat' => 'Lapangan Utama'],
        ]);

        Eskul::where('name', 'Voli')->first()?->jadwals()->create([
            'hari' => 'RABU', 'jam_mulai' => '15:00', 'jam_selesai' => '17:00', 'tempat' => 'Lapangan Voli',
        ]);

        // Catatan: tabel announcements tidak dipakai aplikasi (tidak ada model);
        // pengumuman dikelola via AppSetting/notifikasi. Jangan seed di sini.

        $siswa = User::create([
            'name' => 'Siswa Demo',
            'email' => 'siswa@sekolah.sch.id',
            'password' => Hash::make('password'),
            'role' => Role::STUDENT,
            'username' => 'siswa',
        ]);

        $token = LoginToken::issue($siswa);

        Student::create([
            'nis' => '10001',
            'nama' => 'Siswa Demo',
            'kelas' => 'X RPL 1',
            'jurusan' => 'RPL',
            'gender' => 'LAKI_LAKI',
            'status' => 'AKTIF',
            'user_id' => $siswa->id,
        ]);

        // Siswa kedua: data induk ada, akun + token belum (untuk demo alur TU).
        Student::create([
            'nis' => '10002',
            'nama' => 'Siswi Demo',
            'kelas' => 'X DKV 1',
            'jurusan' => 'DKV',
            'gender' => 'PEREMPUAN',
            'status' => 'AKTIF',
        ]);

        $this->command->info('Admin  : admin@sekolah.sch.id / password');
        $this->command->info('Siswa  : NIS 10001 + token di bawah');
        $this->command->info("Token siswa demo (8 digit): {$token}");
    }
}
