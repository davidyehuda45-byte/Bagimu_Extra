<?php

namespace Database\Seeders;

use App\Enums\PeriodeStatus;
use App\Enums\Role;
use App\Enums\Status;
use App\Models\AppSetting;
use App\Models\Eskul;
use App\Models\PendaftaranWindow;
use App\Models\Periode;
use App\Models\Registration;
use App\Models\Student;
use App\Models\User;
use App\Support\LoginToken;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class QaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Settings
        AppSetting::set('school.name', 'SMK Bagimu Negeriku');
        AppSetting::set('school.address', 'Ngaliyan, Kota Semarang');
        AppSetting::set('app.maintenance', '0');
        AppSetting::set('eskul.kelas_allowed', '10,11');
        AppSetting::set('eskul.semester_aktif', '1');

        // 2. Periode & Window
        Periode::updateOrCreate(
            ['periode' => '2026/2027'],
            [
                'nama' => 'Tahun Ajaran 2026/2027',
                'status' => PeriodeStatus::DIBUKA,
                'max_eskul_per_siswa' => 1,
            ]
        );

        PendaftaranWindow::updateOrCreate(
            ['periode' => '2026/2027'],
            ['buka' => true]
        );

        // 3. Admin Account
        $admin = User::updateOrCreate(
            ['username' => 'davidyehudasurbakti'],
            [
                'name' => 'David Yehuda Surbakti',
                'email' => 'david.yehuda45@smk.belajar.id',
                'password' => Hash::make('Yehuda4545!'),
                'role' => Role::ADMIN,
                'email_verified_at' => now(),
            ]
        );

        // 4. Pembina Account
        $pembina = User::updateOrCreate(
            ['username' => 'pembina_futsal'],
            [
                'name' => 'Budi Pembina Futsal',
                'email' => 'pembina.futsal@sekolah.sch.id',
                'password' => Hash::make('Pembina123!'),
                'role' => Role::PEMBINA,
                'email_verified_at' => now(),
            ]
        );

        // 5. Eskuls
        $futsal = Eskul::updateOrCreate(
            ['name' => 'Futsal'],
            [
                'description' => 'Ekstrakurikuler Sepak Bola Ruangan',
                'kuota' => 20,
                'kuota_aktif' => true,
                'aktif' => true,
                'pembina_id' => $pembina->id,
            ]
        );

        if ($futsal->jadwals()->count() === 0) {
            $futsal->jadwals()->createMany([
                ['hari' => 'SELASA', 'jam_mulai' => '15:00', 'jam_selesai' => '17:00', 'tempat' => 'Lapangan Utama'],
                ['hari' => 'JUMAT', 'jam_mulai' => '15:00', 'jam_selesai' => '17:00', 'tempat' => 'Lapangan Utama'],
            ]);
        }

        $voli = Eskul::updateOrCreate(
            ['name' => 'Voli'],
            [
                'description' => 'Bola Voli Prestasi',
                'kuota' => 15,
                'kuota_aktif' => true,
                'aktif' => true,
            ]
        );

        if ($voli->jadwals()->count() === 0) {
            $voli->jadwals()->create([
                'hari' => 'RABU', 'jam_mulai' => '15:00', 'jam_selesai' => '17:00', 'tempat' => 'Lapangan Voli',
            ]);
        }

        // 6. Student 1: Fresh & Ready to Register
        Registration::whereIn('nis', ['10001', '10002'])->delete();

        $user1 = User::updateOrCreate(
            ['username' => '10001'],
            [
                'name' => 'Ahmad Siswa Satu',
                'email' => '10001@siswa.local',
                'role' => Role::STUDENT,
                'password' => Hash::make('secret12345'),
            ]
        );
        $user1->update([
            'login_token' => 'ABCDEF12',
            'login_token_hash' => LoginToken::hash('ABCDEF12'),
            'login_token_revoked_at' => null,
        ]);

        Student::updateOrCreate(
            ['nis' => '10001'],
            [
                'nama' => 'Ahmad Siswa Satu',
                'kelas' => 'X RPL 1',
                'jurusan' => 'RPL',
                'gender' => 'LAKI_LAKI',
                'status' => 'AKTIF',
                'user_id' => $user1->id,
            ]
        );

        // 7. Student 2: Fresh & Ready to Register
        $user2 = User::updateOrCreate(
            ['username' => '10002'],
            [
                'name' => 'Siti Siswi Dua',
                'email' => '10002@siswa.local',
                'role' => Role::STUDENT,
                'password' => Hash::make('secret12345'),
            ]
        );
        $user2->update([
            'login_token' => 'WXYZ7890',
            'login_token_hash' => LoginToken::hash('WXYZ7890'),
            'login_token_revoked_at' => null,
        ]);

        Student::updateOrCreate(
            ['nis' => '10002'],
            [
                'nama' => 'Siti Siswi Dua',
                'kelas' => 'X DKV 1',
                'jurusan' => 'DKV',
                'gender' => 'PEREMPUAN',
                'status' => 'AKTIF',
                'user_id' => $user2->id,
            ]
        );

        // 8. Student 3: Already Registered to Futsal (Locked)
        $user3 = User::updateOrCreate(
            ['username' => '10003'],
            [
                'name' => 'Rian Siswa Tiga',
                'email' => '10003@siswa.local',
                'role' => Role::STUDENT,
                'password' => Hash::make('secret12345'),
            ]
        );
        $user3->update([
            'login_token' => 'LOCKED01',
            'login_token_hash' => LoginToken::hash('LOCKED01'),
            'login_token_revoked_at' => now(),
        ]);

        $student3 = Student::updateOrCreate(
            ['nis' => '10003'],
            [
                'nama' => 'Rian Siswa Tiga',
                'kelas' => 'X RPL 1',
                'jurusan' => 'RPL',
                'gender' => 'LAKI_LAKI',
                'status' => 'AKTIF',
                'user_id' => $user3->id,
            ]
        );

        Registration::updateOrCreate(
            ['user_id' => $user3->id, 'periode' => '2026/2027'],
            [
                'kelas' => 'X RPL 1',
                'jurusan' => 'RPL',
                'nis' => '10003',
                'no_absen' => 3,
                'nama_wali_kelas' => 'Pak Guru RPL',
                'usia' => 16,
                'gender' => \App\Enums\Gender::LAKI_LAKI,
                'whatsapp' => '081234567893',
                'eskul_utama' => 'Futsal',
                'status' => Status::APPROVED,
                'verify_token' => bin2hex(random_bytes(32)),
            ]
        );
    }
}

