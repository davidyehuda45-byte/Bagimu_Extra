<?php

namespace Tests\Feature;

use App\Enums\PeriodeStatus;
use App\Enums\Role;
use App\Models\AppSetting;
use App\Models\Eskul;
use App\Models\PendaftaranWindow;
use App\Models\Periode;
use App\Models\Registration;
use App\Models\Student;
use App\Models\User;
use App\Support\LoginToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class ParitasTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => Role::ADMIN]);
    }

    private function siapkan(): array
    {
        Periode::create(['periode' => '2026/2027', 'status' => PeriodeStatus::DIBUKA]);
        PendaftaranWindow::create(['periode' => '2026/2027', 'buka' => true]);
        $eskul = Eskul::create(['name' => 'Futsal', 'aktif' => true]);

        Student::create([
            'nis' => '10001', 'nama' => 'Siswa Tes', 'kelas' => '10 RPL 1',
            'jurusan' => 'RPL', 'status' => 'AKTIF',
        ]);
        $siswa = User::factory()->create(['username' => '10001', 'role' => Role::STUDENT]);

        return [$siswa, $eskul];
    }

    private function daftarPayload(array $over = []): array
    {
        return array_merge([
            'kelas' => '10 RPL 1',
            'no_absen' => 5,
            'nama_wali_kelas' => 'Bu Wali',
            'jurusan' => 'RPL',
            'usia' => 16,
            'gender' => 'LAKI_LAKI',
            'whatsapp' => '081234567890',
            'eskul_utama' => 'Futsal',
            'alasan' => 'Ingin sehat.',
        ], $over);
    }

    public function test_daftar_langsung_disetujui(): void
    {
        [$siswa] = $this->siapkan();

        $this->actingAs($siswa)->post('/siswa/daftar', $this->daftarPayload())
            ->assertRedirect('/siswa/status');

        $this->assertDatabaseHas('registrations', ['user_id' => $siswa->id, 'status' => 'APPROVED']);
        // Nomor WA otomatis tersambung ke direktori siswa.
        $this->assertDatabaseHas('students', ['nis' => '10001', 'whatsapp' => '081234567890']);
    }

    public function test_kuota_penuh_ditolak_tanpa_cadangan(): void
    {
        [$siswa, $eskul] = $this->siapkan();
        $eskul->update(['kuota_aktif' => true, 'kuota' => 1]);

        $penuh = User::factory()->create(['username' => '90001', 'role' => Role::STUDENT]);
        Registration::create([
            'user_id' => $penuh->id, 'periode' => '2026/2027', 'kelas' => '10 RPL 2',
            'nis' => '90001', 'jurusan' => 'RPL', 'usia' => 16, 'gender' => 'LAKI_LAKI',
            'eskul_utama' => 'Futsal', 'status' => 'APPROVED',
        ]);

        // Kuota penuh → ditolak dengan pesan penuh (tanpa pengalihan cadangan).
        $this->actingAs($siswa)->post('/siswa/daftar', $this->daftarPayload())
            ->assertSessionHasErrors('eskul_utama');
    }

    public function test_predikat_mengikuti_rumus_lama(): void
    {
        [$siswa, $eskul] = $this->siapkan();
        $reg = Registration::create([
            'user_id' => $siswa->id, 'periode' => '2026/2027', 'kelas' => '10 RPL 1',
            'nis' => '10001', 'jurusan' => 'RPL', 'usia' => 16, 'gender' => 'LAKI_LAKI',
            'eskul_utama' => 'Futsal', 'status' => 'APPROVED',
        ]);

        foreach ([[80, 'B'], [60, 'C'], [50, 'D'], [90, 'A']] as [$nilai, $predikat]) {
            $this->actingAs($this->admin())->post("/admin/registrations/{$reg->id}/nilai", [
                'nilai' => $nilai,
            ])->assertRedirect();

            $this->assertSame($predikat, $reg->fresh()->predikat);
        }
    }

    public function test_nomor_sertifikat_format_lama(): void
    {
        [$siswa, $eskul] = $this->siapkan();
        $reg = Registration::create([
            'user_id' => $siswa->id, 'periode' => '2026/2027', 'kelas' => '10 RPL 1',
            'nis' => '10001', 'jurusan' => 'RPL', 'usia' => 16, 'gender' => 'LAKI_LAKI',
            'eskul_utama' => 'Futsal', 'status' => 'APPROVED',
        ]);

        $this->actingAs($this->admin())->post('/admin/certificates', [
            'registration_id' => $reg->id,
        ])->assertRedirect();

        $nomor = $reg->fresh()->certificate->nomor;
        $this->assertMatchesRegularExpression('/^SRT-\d{4}-[A-HJ-NP-Z2-9]{6}$/', $nomor);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $reg->fresh()->verify_token);
    }

    public function test_kode_pendaftaran_dan_verifikasi(): void
    {
        [$siswa, $eskul] = $this->siapkan();
        $reg = Registration::create([
            'user_id' => $siswa->id, 'periode' => '2026/2027', 'kelas' => '10 RPL 1',
            'nis' => '10001', 'jurusan' => 'RPL', 'usia' => 16, 'gender' => 'LAKI_LAKI',
            'eskul_utama' => 'Futsal', 'status' => 'APPROVED',
            'verify_token' => bin2hex(random_bytes(32)),
        ]);

        $this->assertMatchesRegularExpression('/^ESK-[0-9A-Z]{10}$/', $reg->kode());

        // Verifikasi via kode ESK-…
        $this->get('/verify?kode='.$reg->kode())->assertOk()->assertSee('TERDAFTAR');

        // Kartu dicabut → tidak berlaku.
        $reg->update(['card_revoked_at' => now()]);
        $this->get('/verify/'.$reg->verify_token)->assertOk()->assertSee('tidak berlaku');
    }

    public function test_hapus_data_pendaftaran(): void
    {
        [$siswa, $eskul] = $this->siapkan();

        $a = Registration::create([
            'user_id' => $siswa->id, 'periode' => '2026/2027', 'kelas' => '10 RPL 1',
            'nis' => '10001', 'jurusan' => 'RPL', 'usia' => 16, 'gender' => 'LAKI_LAKI',
            'eskul_utama' => 'Futsal', 'status' => 'APPROVED',
        ]);

        $admin = $this->admin();

        $this->actingAs($admin)->delete("/admin/registrations/{$a->id}")->assertRedirect();
        $this->assertDatabaseMissing('registrations', ['id' => $a->id]);
    }

    public function test_cabut_token_password_sertifikat_detail(): void
    {
        [$user, $eskul] = $this->siapkan();
        $token = LoginToken::issue($user);

        $reg = Registration::create([
            'user_id' => $user->id, 'periode' => '2026/2027', 'kelas' => '10 RPL 1',
            'nis' => '10001', 'jurusan' => 'RPL', 'usia' => 16, 'gender' => 'LAKI_LAKI',
            'eskul_utama' => 'Futsal', 'status' => 'APPROVED',
            'verify_token' => bin2hex(random_bytes(32)),
        ]);

        $admin = $this->admin();

        $this->actingAs($admin)->post("/admin/registrations/{$reg->id}/cabut")->assertRedirect();
        $this->assertNotNull($reg->fresh()->card_revoked_at);

        $this->actingAs($admin)->post("/admin/registrations/{$reg->id}/token")->assertRedirect()
            ->assertSessionHas('status');
        $this->assertNotSame($token, $user->fresh()->login_token);

        $this->actingAs($admin)->post("/admin/registrations/{$reg->id}/password", [
            'password' => 'rahasia-baru',
        ])->assertRedirect();
        $this->assertTrue(Hash::check('rahasia-baru', $user->fresh()->password));

        // Sertifikat idempoten.
        $this->actingAs($admin)->post("/admin/registrations/{$reg->id}/sertifikat")->assertRedirect();
        $this->actingAs($admin)->post("/admin/registrations/{$reg->id}/sertifikat")->assertRedirect();
        $this->assertSame(1, \App\Models\Certificate::where('registration_id', $reg->id)->count());
    }

    public function test_password_siswa_dan_login_alternatif(): void
    {
        [$siswa] = $this->siapkan();

        // Atur password (belum pernah → tanpa password lama).
        $this->actingAs($siswa)->post('/siswa/profil/password', [
            'password' => 'sandi-siswa-1',
            'password_confirmation' => 'sandi-siswa-1',
        ])->assertRedirect();
        $this->assertNotNull($siswa->fresh()->password_set_at);

        // Login pakai NIS + password.
        $this->post('/logout');
        $this->post('/siswa/login', ['nis' => '10001', 'token' => 'sandi-siswa-1'])
            ->assertRedirect('/siswa/daftar');
        $this->assertAuthenticatedAs($siswa);
    }

    public function test_ecard_disetujui_dan_qr(): void
    {
        [$siswa] = $this->siapkan();
        Registration::create([
            'user_id' => $siswa->id, 'periode' => '2026/2027', 'kelas' => '10 RPL 1',
            'nis' => '10001', 'jurusan' => 'RPL', 'usia' => 16, 'gender' => 'LAKI_LAKI',
            'eskul_utama' => 'Futsal', 'status' => 'APPROVED',
            'verify_token' => bin2hex(random_bytes(32)),
        ]);

        $this->actingAs($siswa)->get('/siswa/status')->assertOk()->assertSee('E-Card');
    }

    public function test_halaman_admin_baru(): void
    {
        $this->siapkan();
        $admin = $this->admin();

        foreach ([
            '/admin/rekap', '/admin/kelas', '/admin/window', '/admin/notifikasi',
            '/admin/cetak', '/admin/kenaikan/arsip-keluar', '/admin/settings',
        ] as $url) {
            $this->actingAs($admin)->get($url)->assertOk($url);
        }

        $this->actingAs($admin)->get('/verify')->assertOk();
    }

    public function test_kepala_sekolah_baca_saja(): void
    {
        [$siswa, $eskul] = $this->siapkan();
        $kepsek = User::factory()->create(['role' => Role::KEPALA_SEKOLAH]);

        $this->actingAs($kepsek)->get('/admin/registrations')->assertOk();
        $this->actingAs($kepsek)->get('/admin/laporan')->assertOk();

        $reg = Registration::create([
            'user_id' => $siswa->id, 'periode' => '2026/2027', 'kelas' => '10 RPL 1',
            'nis' => '10001', 'jurusan' => 'RPL', 'usia' => 16, 'gender' => 'LAKI_LAKI',
            'eskul_utama' => 'Futsal', 'status' => 'APPROVED',
        ]);

        $this->actingAs($kepsek)->delete("/admin/registrations/{$reg->id}")
            ->assertForbidden();
        $this->assertDatabaseHas('registrations', ['id' => $reg->id]);
    }

    public function test_maintenance_memblokir_mutasi_siswa(): void
    {
        [$siswa] = $this->siapkan();
        AppSetting::set('app.maintenance', '1');

        $this->actingAs($siswa)->post('/siswa/daftar', $this->daftarPayload())
            ->assertStatus(503);

        // Admin tetap bisa (baca + kelola).
        $reg = Registration::create([
            'user_id' => $siswa->id, 'periode' => '2026/2027', 'kelas' => '10 RPL 1',
            'nis' => '10001', 'jurusan' => 'RPL', 'usia' => 16, 'gender' => 'LAKI_LAKI',
            'eskul_utama' => 'Futsal', 'status' => 'APPROVED',
        ]);
        $this->actingAs($this->admin())->get("/admin/registrations/{$reg->id}")->assertOk();
    }

    public function test_mfa_penuh(): void
    {
        $admin = $this->admin();

        // Aktifkan.
        $this->actingAs($admin)->post('/admin/settings/mfa')->assertRedirect();
        $secret = Crypt::decryptString($admin->fresh()->totp_secret_enc);
        $otp = (new Google2FA)->getCurrentOtp($secret);

        $this->actingAs($admin)->post('/admin/settings/mfa/confirm', ['code' => $otp])
            ->assertRedirect();
        $this->assertTrue($admin->fresh()->totp_enabled);

        // Login meminta MFA.
        $this->post('/logout');
        $this->post('/admin/login', ['login' => $admin->email, 'password' => 'password'])
            ->assertRedirect('/admin/mfa');
        $this->assertGuest();

        // Kode salah ditolak.
        $this->post('/admin/mfa', ['code' => '000000'])->assertSessionHasErrors('code');

        // Kode benar masuk.
        $otp = (new Google2FA)->getCurrentOtp($secret);
        $this->post('/admin/mfa', ['code' => $otp])->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_transisi_periode_dan_hapus_arsip(): void
    {
        $this->admin();
        Periode::create(['periode' => '2025/2026', 'status' => PeriodeStatus::DRAFT]);

        // Transisi ilegal DRAFT → DITUTUP ditolak.
        $this->actingAs(User::first())->put('/admin/periodes/2025/2026', [
            'status' => 'DITUTUP', 'max_eskul_per_siswa' => 1,
        ])->assertSessionHasErrors('status');

        // Transisi legal DRAFT → DIBUKA.
        $this->actingAs(User::first())->put('/admin/periodes/2025/2026', [
            'status' => 'DIBUKA', 'max_eskul_per_siswa' => 1,
        ])->assertRedirect();

        // Periode aktif tak bisa dihapus.
        $this->actingAs(User::first())->delete('/admin/periodes/2025/2026')
            ->assertSessionHasErrors('periode');

        // Tutup dulu lalu hapus (arsip): pendaftar ikut terhapus, user tetap.
        $siswa = User::factory()->create(['username' => '10001', 'role' => Role::STUDENT]);
        Registration::create([
            'user_id' => $siswa->id, 'periode' => '2025/2026', 'kelas' => '10 RPL 1',
            'nis' => '10001', 'jurusan' => 'RPL', 'usia' => 16, 'gender' => 'LAKI_LAKI',
            'eskul_utama' => 'Futsal', 'status' => 'APPROVED',
        ]);
        $this->actingAs(User::first())->put('/admin/periodes/2025/2026', [
            'status' => 'DIARSIPKAN', 'max_eskul_per_siswa' => 1,
        ])->assertRedirect();
        $this->actingAs(User::first())->delete('/admin/periodes/2025/2026')->assertRedirect();
        $this->assertDatabaseMissing('periodes', ['periode' => '2025/2026']);
        $this->assertDatabaseMissing('registrations', ['periode' => '2025/2026']);
        $this->assertDatabaseHas('users', ['username' => '10001']);
    }

    public function test_kelas_romawi_diterima_dan_dinormalisasi(): void
    {
        [$siswa] = $this->siapkan();

        $this->actingAs($siswa)->post('/siswa/daftar', $this->daftarPayload(['kelas' => 'XI RPL 1']))
            ->assertRedirect('/siswa/status');

        $this->assertDatabaseHas('registrations', ['user_id' => $siswa->id, 'kelas' => '11 RPL 1']);
        $this->assertSame(11, \App\Support\Kelas::tingkat('XI Kuliner 2'));
        $this->assertSame('12 TKJ', \App\Support\Kelas::naikkan('XI TKJ'));
    }

    public function test_export_excel_dan_token_admin_saja(): void
    {
        [$siswa, $eskul] = $this->siapkan();
        Registration::create([
            'user_id' => $siswa->id, 'periode' => '2026/2027', 'kelas' => '10 RPL 1',
            'nis' => '10001', 'jurusan' => 'RPL', 'usia' => 16, 'gender' => 'LAKI_LAKI',
            'eskul_utama' => 'Futsal', 'status' => 'APPROVED',
        ]);

        $res = $this->actingAs($this->admin())
            ->get('/admin/registrations/export?periode=2026/2027&format=excel');
        $res->assertOk();
        $this->assertStringContainsString('ms-excel', $res->headers->get('Content-Type'));
        $this->assertStringContainsString('Token Login', $res->streamedContent());

        $kepsek = User::factory()->create(['role' => Role::KEPALA_SEKOLAH]);
        $res = $this->actingAs($kepsek)
            ->get('/admin/registrations/export?periode=2026/2027');
        $res->assertOk();
        $this->assertStringNotContainsString('Token Login', $res->streamedContent());
    }

    public function test_nisn_tersimpan_dan_unik(): void
    {
        $this->siapkan();
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/students', [
            'nis' => '50001', 'nisn' => '0050010001', 'nama' => 'Anak NISN',
            'kelas' => '10 RPL 1', 'jurusan' => 'RPL', 'status' => 'AKTIF',
        ])->assertRedirect();

        $this->assertDatabaseHas('students', ['nis' => '50001', 'nisn' => '0050010001']);

        $this->actingAs($admin)->get('/admin/students?q=0050010001')->assertOk()->assertSee('0050010001');
    }

    public function test_kenaikan_kelas_otomatis(): void
    {
        $this->siapkan();
        $admin = $this->admin();

        Student::create(['nis' => '20001', 'nama' => 'Kelas X', 'kelas' => '10 RPL 1', 'jurusan' => 'RPL', 'status' => 'AKTIF']);
        Student::create(['nis' => '20002', 'nama' => 'Kelas XI', 'kelas' => '11 RPL 1', 'jurusan' => 'RPL', 'status' => 'AKTIF']);
        $u = User::factory()->create(['username' => '20002', 'role' => Role::STUDENT]);
        Student::where('nis', '20002')->update(['user_id' => $u->id]);
        Student::create(['nis' => '20003', 'nama' => 'Kelas XII', 'kelas' => '12 RPL 1', 'jurusan' => 'RPL', 'status' => 'AKTIF']);

        $this->actingAs($admin)->get('/admin/kenaikan')->assertOk()->assertSee('Kenaikan Kelas');

        $this->actingAs($admin)->post('/admin/kenaikan', ['konfirmasi' => '1'])->assertRedirect();

        $this->assertSame('11 RPL 1', Student::where('nis', '20001')->first()->kelas);
        $this->assertSame('12 RPL 1', Student::where('nis', '20002')->first()->kelas);
        $this->assertSame('NONAKTIF', Student::where('nis', '20002')->first()->status);
        $this->assertSame('NONAKTIF', $u->fresh()->student_status->value);
        $this->assertSame('LULUS', Student::where('nis', '20003')->first()->status);
    }

    public function test_syarat_kelayakan_ditegakkan(): void
    {
        [$siswa] = $this->siapkan();

        // Kelas 12 tidak boleh daftar.
        $res = $this->actingAs($siswa)->post('/siswa/daftar', $this->daftarPayload(['kelas' => '12 RPL 1']));
        $res->assertSessionHasErrors('kelas');

        // Kelas 11 semester 2 (magang) tidak boleh.
        AppSetting::set('eskul.semester_aktif', '2');
        $res = $this->actingAs($siswa)->post('/siswa/daftar', $this->daftarPayload(['kelas' => '11 RPL 1']));
        $res->assertSessionHasErrors('kelas');

        // Kelas 11 semester 1 boleh.
        AppSetting::set('eskul.semester_aktif', '1');
        $this->actingAs($siswa)->post('/siswa/daftar', $this->daftarPayload(['kelas' => '11 RPL 1']))
            ->assertRedirect('/siswa/status');
    }

    public function test_undangan_staf(): void
    {
        $this->admin();

        $this->actingAs(User::first())->get('/admin/invitations')->assertOk();

        $res = $this->actingAs(User::first())->post('/admin/invitations', [
            'name' => 'Calon Admin', 'email' => 'calon@sekolah.sch.id', 'role' => 'OPERATOR',
        ]);
        $res->assertOk()->assertSee('calon@sekolah.sch.id')->assertSee('/admin/login');

        $this->assertDatabaseHas('users', ['email' => 'calon@sekolah.sch.id', 'role' => 'OPERATOR']);
    }

    public function test_pembina_tanpa_akses_data_admin(): void
    {
        [$siswa, $eskul] = $this->siapkan();
        $pembina = User::factory()->create(['role' => Role::PEMBINA]);
        $eskul->update(['pembina_id' => $pembina->id]);

        $this->actingAs($pembina)->get('/admin/kehadiran')->assertForbidden();
        $this->actingAs($pembina)->get('/admin/nilai')->assertForbidden();
        $this->actingAs($pembina)->get('/admin/izin')->assertForbidden();

        // Izin eskul sendiri tetap bisa diputus.
        $reg = Registration::create([
            'user_id' => $siswa->id, 'periode' => '2026/2027', 'kelas' => '10 RPL 1',
            'nis' => '10001', 'jurusan' => 'RPL', 'usia' => 16, 'gender' => 'LAKI_LAKI',
            'eskul_utama' => 'Futsal', 'status' => 'APPROVED',
        ]);
        $izin = \App\Models\Izin::create([
            'registration_id' => $reg->id, 'eskul_id' => $eskul->id,
            'tanggal' => '2026-10-20', 'jenis' => 'IZIN', 'alasan' => 'Acara keluarga',
            'status' => 'PENDING',
        ]);

        $this->actingAs($pembina)->post("/pembina/izin/{$izin->id}/approve")->assertRedirect();
        $this->assertSame('APPROVED', $izin->fresh()->status->value);
    }

    public function test_urutan_absen_nama(): void
    {
        [$siswa, $eskul] = $this->siapkan();
        $budi = User::factory()->create(['username' => '30002', 'name' => 'Budi', 'role' => Role::STUDENT]);
        $andi = User::factory()->create(['username' => '30001', 'name' => 'Andi', 'role' => Role::STUDENT]);

        foreach ([[$budi, '30002'], [$andi, '30001']] as [$u, $nis]) {
            Registration::create([
                'user_id' => $u->id, 'periode' => '2026/2027', 'kelas' => '10 RPL 1',
                'nis' => $nis, 'jurusan' => 'RPL', 'usia' => 16, 'gender' => 'LAKI_LAKI',
                'eskul_utama' => 'Futsal', 'status' => 'APPROVED',
            ]);
        }

        $res = $this->actingAs($this->admin())->get('/admin/rekap/Futsal');
        $res->assertOk();
        $html = $res->getContent();
        $posAndi = strpos($html, 'Andi');
        $posBudi = strpos($html, 'Budi');
        $this->assertNotFalse($posAndi);
        $this->assertNotFalse($posBudi);
        $this->assertLessThan($posBudi, $posAndi);
    }
}
