<?php

namespace Database\Seeders;

use App\Enums\Gender;
use App\Enums\Jurusan;
use App\Enums\PeriodeStatus;
use App\Enums\Role;
use App\Enums\Status;
use App\Models\Eskul;
use App\Models\Periode;
use App\Models\Registration;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Support\LoginToken;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestAccountSeeder extends Seeder
{
    /**
     * Jalankan seeder akun uji untuk keperluan testing lokal/staging.
     */
    public function run(): void
    {
        // 1. Pengaman lingkungan: HANYA boleh jalan di local, staging, atau testing
        if (! app()->environment('local', 'staging', 'testing')) {
            $this->command->error('Seeder ini DIBLOKIR di luar lingkungan local/staging!');
            return;
        }

        $this->command->info('Membersihkan akun uji lama jika ada...');

        // Bersihkan pendaftaran & data akun uji lama
        $testUserIds = User::where('email', 'like', '%@test.local')
            ->orWhere('username', 'like', 'uji_%')
            ->orWhere('username', 'like', 'TEST%')
            ->pluck('id');

        Registration::whereIn('user_id', $testUserIds)
            ->orWhere('nis', 'like', 'TEST%')
            ->delete();

        Student::where('nis', 'like', 'TEST%')
            ->orWhereIn('user_id', $testUserIds)
            ->delete();

        User::whereIn('id', $testUserIds)->delete();

        // 2. Pastikan ada Periode aktif
        $periode = Periode::where('status', PeriodeStatus::DIBUKA)->first();
        if (! $periode) {
            $periode = Periode::firstOrCreate(
                ['periode' => '2026/2027'],
                [
                    'nama' => 'Tahun Ajaran 2026/2027',
                    'status' => PeriodeStatus::DIBUKA,
                    'max_eskul_per_siswa' => 1,
                ]
            );
        }

        // 3. Pastikan ada 1 Eskul untuk uji pembina
        $eskul = Eskul::first();
        if (! $eskul) {
            $eskul = Eskul::create([
                'name' => 'Futsal Uji',
                'description' => 'Ekstrakurikuler Futsal untuk akun uji',
                'kuota' => 25,
                'kuota_aktif' => true,
                'aktif' => true,
            ]);
        }

        // 4. Pastikan ada 1 Kelas untuk uji wali kelas & siswa
        $kelas = SchoolClass::first();
        if (! $kelas) {
            $kelas = SchoolClass::create([
                'nama' => 'X RPL 1',
                'tingkat' => 10,
                'jurusan' => 'RPL',
                'tahun_ajaran' => $periode->periode,
            ]);
        }

        // Generate password acak kuat
        $adminPassword = 'Adm_' . Str::random(10) . '!';
        $pembinaPassword = 'Pem_' . Str::random(10) . '!';
        $waliPassword = 'Wal_' . Str::random(10) . '!';

        // 5. Akun Admin
        $admin = User::create([
            'name' => 'Uji Administrator',
            'username' => 'uji_admin',
            'email' => 'admin@test.local',
            'password' => Hash::make($adminPassword),
            'role' => Role::ADMIN,
            'email_verified_at' => now(),
        ]);

        // 6. Akun Pembina (terhubung ke 1 eskul)
        $pembina = User::create([
            'name' => 'Uji Pembina ' . $eskul->name,
            'username' => 'uji_pembina',
            'email' => 'pembina@test.local',
            'password' => Hash::make($pembinaPassword),
            'role' => Role::PEMBINA,
            'email_verified_at' => now(),
        ]);
        $eskul->update(['pembina_id' => $pembina->id]);

        // 7. Akun Wali Kelas (terhubung ke 1 kelas)
        $waliKelas = User::create([
            'name' => 'Uji Wali Kelas ' . $kelas->nama,
            'username' => 'uji_walikelas',
            'email' => 'walikelas@test.local',
            'password' => Hash::make($waliPassword),
            'role' => Role::WALI_KELAS,
            'email_verified_at' => now(),
        ]);
        $kelas->update([
            'wali_kelas_id' => $waliKelas->id,
            'wali_kelas_nama' => $waliKelas->name,
        ]);

        // 8. Siswa 1: Fresh (Belum Mendaftar Eskul)
        $userSiswa1 = User::create([
            'name' => 'Uji Siswa Satu',
            'username' => 'TEST1001',
            'email' => 'test1001@test.local',
            'role' => Role::STUDENT,
            'password' => Hash::make(Str::random(32)),
        ]);
        $tokenSiswa1 = LoginToken::issue($userSiswa1);

        Student::create([
            'nis' => 'TEST1001',
            'nisn' => '0098765431',
            'nama' => 'Uji Siswa Satu',
            'kelas' => $kelas->nama,
            'jurusan' => Jurusan::RPL,
            'gender' => Gender::LAKI_LAKI,
            'no_absen' => 1,
            'status' => 'AKTIF',
            'user_id' => $userSiswa1->id,
            'wali_nama' => $waliKelas->name,
        ]);

        // 9. Siswa 2: Sudah Mendaftar (Terkunci & Token Hangus)
        $userSiswa2 = User::create([
            'name' => 'Uji Siswa Dua',
            'username' => 'TEST1002',
            'email' => 'test1002@test.local',
            'role' => Role::STUDENT,
            'password' => Hash::make(Str::random(32)),
        ]);
        $tokenSiswa2 = LoginToken::issue($userSiswa2);
        LoginToken::revoke($userSiswa2);

        $student2 = Student::create([
            'nis' => 'TEST1002',
            'nisn' => '0098765432',
            'nama' => 'Uji Siswa Dua',
            'kelas' => $kelas->nama,
            'jurusan' => Jurusan::RPL,
            'gender' => Gender::PEREMPUAN,
            'no_absen' => 2,
            'status' => 'AKTIF',
            'user_id' => $userSiswa2->id,
            'wali_nama' => $waliKelas->name,
        ]);

        Registration::create([
            'user_id' => $userSiswa2->id,
            'periode' => $periode->periode,
            'kelas' => $kelas->nama,
            'jurusan' => 'RPL',
            'nis' => $student2->nis,
            'no_absen' => 2,
            'nama_wali_kelas' => $waliKelas->name,
            'usia' => 16,
            'gender' => Gender::PEREMPUAN->value,
            'whatsapp' => '081234567890',
            'eskul_utama' => $eskul->name,
            'status' => Status::APPROVED->value,
            'verify_token' => bin2hex(random_bytes(32)),
        ]);

        // 10. Tampilkan kredensial sekali di output terminal
        $this->command->newLine();
        $this->command->info('================================================================');
        $this->command->info('                 AKUN UJI BERHASIL DIBUAT                       ');
        $this->command->info('================================================================');
        $this->command->table(
            ['Peran', 'URL Login', 'Username / NIS', 'Password / Token', 'Catatan'],
            [
                ['Admin', '/admin/login', 'uji_admin', $adminPassword, 'Akses penuh admin'],
                ['Pembina', '/pembina/login', 'uji_pembina', $pembinaPassword, 'Eskul: ' . $eskul->name],
                ['Wali Kelas', '/pembina/login atau /admin/login', 'uji_walikelas', $waliPassword, 'Kelas: ' . $kelas->nama],
                ['Siswa (Fresh)', '/siswa/login', 'TEST1001', $tokenSiswa1, 'Belum daftar (Siap uji)'],
                ['Siswa (Locked)', '/siswa/login', 'TEST1002', $tokenSiswa2, 'Sudah daftar (Token hangus)'],
            ]
        );
        $this->command->warn('CATATAN KEAMANAN: Kredensial di atas di-generate acak dan HANYA ditampilkan di sini.');
        $this->command->warn('Untuk membersihkan akun uji kapan saja, jalankan: php artisan test-accounts:clear');
        $this->command->newLine();
    }
}
