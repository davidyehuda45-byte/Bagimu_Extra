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
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => Role::ADMIN]);
    }

    private function pembina(): User
    {
        return User::factory()->create(['role' => Role::PEMBINA]);
    }

    private function siswa(string $nis = '10001'): User
    {
        Student::create([
            'nis' => $nis,
            'nama' => 'Siswa Tes',
            'kelas' => 'X RPL 1',
            'jurusan' => 'RPL',
            'status' => 'AKTIF',
        ]);

        return User::factory()->create(['username' => $nis, 'role' => Role::STUDENT]);
    }

    private function eskul(string $name = 'Futsal', ?User $pembina = null): Eskul
    {
        return Eskul::create([
            'name' => $name,
            'aktif' => true,
            'pembina_id' => $pembina?->id,
        ]);
    }

    private function periodeTerbuka(): void
    {
        Periode::create(['periode' => '2026/2027', 'status' => PeriodeStatus::DIBUKA]);
        PendaftaranWindow::create(['periode' => '2026/2027', 'buka' => true]);
    }

    private function registrasi(User $user, string $status = 'PENDING'): Registration
    {
        return Registration::create([
            'user_id' => $user->id,
            'periode' => '2026/2027',
            'kelas' => 'X RPL 1',
            'nis' => '10001',
            'jurusan' => 'RPL',
            'usia' => 16,
            'gender' => 'LAKI_LAKI',
            'eskul_utama' => 'Futsal',
            'status' => $status,
        ]);
    }

    public function test_halaman_publik(): void
    {
        $this->get('/')->assertOk();
        $this->get('/siswa/login')->assertOk();
        $this->get('/admin/login')->assertOk();
        $this->get('/verify/token-salah')->assertNotFound();
    }

    public function test_penjagaan_akses(): void
    {
        $siswa = $this->siswa();
        $admin = $this->admin();

        $this->get('/siswa')->assertRedirect('/login');
        $this->actingAs($siswa)->get('/admin')->assertForbidden();
        $this->actingAs($admin)->get('/siswa')->assertForbidden();
    }

    public function test_halaman_siswa(): void
    {
        $this->periodeTerbuka();
        $this->eskul();
        $siswa = $this->siswa();
        $siswaTerdaftar = $this->siswa('10002');
        $this->registrasi($siswaTerdaftar, 'APPROVED');

        foreach ([
            '/siswa', '/siswa/daftar', '/siswa/status',
            '/siswa/kehadiran', '/siswa/profil',
        ] as $url) {
            $this->actingAs($siswa)->get($url)->assertOk($url);
        }

        foreach (['/siswa/izin', '/siswa/izin/buat'] as $url) {
            $this->actingAs($siswaTerdaftar)->get($url)->assertOk($url);
        }
    }

    public function test_alur_daftar_lalu_disetujui(): void
    {
        $this->periodeTerbuka();
        $this->eskul();
        $siswa = $this->siswa();
        $admin = $this->admin();

        $res = $this->actingAs($siswa)->post('/siswa/daftar', [
            'kelas' => '10 RPL 1',
            'no_absen' => 5,
            'nama_wali_kelas' => 'Bu Wali',
            'jurusan' => 'RPL',
            'usia' => 16,
            'gender' => 'LAKI_LAKI',
            'whatsapp' => '081234567890',
            'eskul_utama' => 'Futsal',
            'alasan' => 'Ingin sehat.',
        ]);
        $res->assertRedirect('/siswa/status');
        $this->assertDatabaseHas('registrations', ['user_id' => $siswa->id, 'status' => 'APPROVED']);
        $this->assertDatabaseHas('status_history', ['new_status' => 'APPROVED']);

        // Kirim kedua = ditolak (pilihan final, tidak dapat diubah).
        $this->actingAs($siswa)->from('/siswa/daftar')->post('/siswa/daftar', [
            'kelas' => '10 RPL 1',
            'no_absen' => 5,
            'nama_wali_kelas' => 'Bu Wali',
            'jurusan' => 'RPL',
            'usia' => 16,
            'gender' => 'LAKI_LAKI',
            'whatsapp' => '081999999999',
            'eskul_utama' => 'Futsal',
            'alasan' => 'Coba lagi.',
        ])->assertRedirect('/siswa/status');
        $this->assertDatabaseMissing('registrations', ['user_id' => $siswa->id, 'whatsapp' => '081999999999']);

        $reg = Registration::where('user_id', $siswa->id)->first();

        // Admin melihat detail (tanpa persetujuan — langsung tercatat).
        $this->actingAs($admin)->get("/admin/registrations/{$reg->id}")
            ->assertOk()
            ->assertSee($reg->kode());
        $this->assertSame('APPROVED', $reg->fresh()->status->value);
    }

    public function test_admin_menghapus_data(): void
    {
        $this->periodeTerbuka();
        $this->eskul();
        $siswa = $this->siswa();
        $reg = $this->registrasi($siswa);

        $this->actingAs($this->admin())->delete("/admin/registrations/{$reg->id}")
            ->assertRedirect();
        $this->assertDatabaseMissing('registrations', ['id' => $reg->id]);
    }

    public function test_halaman_admin(): void
    {
        $this->periodeTerbuka();
        $this->eskul();
        $admin = $this->admin();

        foreach ([
            '/admin', '/admin/eskuls', '/admin/eskuls/create', '/admin/periodes',
            '/admin/registrations', '/admin/students',
            '/admin/students/import', '/admin/kehadiran', '/admin/nilai',
            '/admin/izin', '/admin/certificates', '/admin/inventory',
            '/admin/inventory/create', '/admin/laporan', '/admin/logs',
            '/admin/settings', '/admin/rekap', '/admin/kelas', '/admin/window',
            '/admin/notifikasi', '/admin/cetak', '/admin/kartu',
            '/eskul', '/jadwal', '/verify',
        ] as $url) {
            $this->actingAs($admin)->get($url)->assertOk($url);
        }
    }

    public function test_halaman_pembina_dan_scope(): void
    {
        $me = $this->pembina();
        $other = $this->pembina();
        $milik = $this->eskul('Futsal', $me);
        $bukan = $this->eskul('Basket', $other);

        foreach (['/pembina', '/pembina/eskul', '/pembina/kegiatan', '/pembina/kegiatan/create'] as $url) {
            $this->actingAs($me)->get($url)->assertOk($url);
        }

        $this->actingAs($me)->get("/pembina/eskul/{$milik->id}")->assertOk();
        $this->actingAs($me)->get("/pembina/eskul/{$bukan->id}")->assertForbidden();
    }

    public function test_terbit_token_dan_kartu(): void
    {
        $admin = $this->admin();
        $student = Student::create([
            'nis' => '20001', 'nama' => 'Baru', 'kelas' => 'X TP 1',
            'jurusan' => 'TP', 'status' => 'AKTIF',
        ]);

        $res = $this->actingAs($admin)->post("/admin/students/{$student->id}/token");
        $res->assertRedirect();
        $res->assertSessionHas('status');

        $user = User::where('username', '20001')->firstOrFail();
        $this->assertNotNull($user->login_token_hash);

        // Login dengan token yang baru diterbitkan tidak bisa ditebak dari
        // session, jadi uji revoke + cetak kartu (terbitkan lagi + tampil).
        $this->actingAs($admin)->post("/admin/students/{$student->id}/kartu")
            ->assertOk();
    }

    public function test_sertifikat_dan_verifikasi_publik(): void
    {
        $this->periodeTerbuka();
        $this->eskul();
        $siswa = $this->siswa();
        $reg = $this->registrasi($siswa, 'APPROVED');

        $this->actingAs($this->admin())->post('/admin/certificates', [
            'registration_id' => $reg->id,
        ])->assertRedirect();
        $this->assertDatabaseHas('certificates', ['registration_id' => $reg->id]);

        $cert = $reg->fresh()->certificate;
        $this->actingAs($this->admin())->get("/admin/certificates/{$cert->id}/cetak")->assertOk();
        $this->get('/verify/'.$reg->fresh()->verify_token)->assertOk();
    }

    public function test_kehadiran_simpan_dan_kunci(): void
    {
        $this->periodeTerbuka();
        $eskul = $this->eskul();
        $siswa = $this->siswa();
        $reg = $this->registrasi($siswa, 'APPROVED');
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/kehadiran', [
            'eskul_id' => $eskul->id,
            'tanggal' => '2026-10-13',
            'status' => [$reg->id => 'HADIR'],
        ])->assertRedirect();
        $this->assertDatabaseHas('kehadiran', [
            'registration_id' => $reg->id, 'status' => 'HADIR',
        ]);

        $this->actingAs($admin)->post('/admin/kehadiran/lock', [
            'eskul_id' => $eskul->id,
            'tanggal' => '2026-10-13',
        ])->assertRedirect();
        $this->assertNotNull(
            \App\Models\Kehadiran::where('registration_id', $reg->id)->first()->locked_at
        );
    }

    public function test_import_csv_dan_pengaturan(): void
    {
        $admin = $this->admin();
        $csv = "nis;nama;kelas;jurusan\n30001;Andi;X RPL 2;RPL\n30002;Budi;X DKV 1;DKV\n";
        $file = UploadedFile::fake()->createWithContent('siswa.csv', $csv);

        $this->actingAs($admin)->post('/admin/students/import', ['file' => $file])
            ->assertRedirect();
        $this->assertDatabaseHas('students', ['nis' => '30001']);
        $this->assertDatabaseHas('students', ['nis' => '30002']);

        $this->actingAs($admin)->put('/admin/settings', [
            'school_name' => 'SMK Bagimu',
        ])->assertRedirect();
        $this->assertSame('SMK Bagimu', AppSetting::get('school.name'));
    }

    public function test_login_siswa_nis_token_asli(): void
    {
        $user = $this->siswa('10001');
        $token = LoginToken::issue($user);

        $this->post('/siswa/login', ['nis' => '10001', 'token' => $token])
            ->assertRedirect('/siswa/daftar');
        $this->assertAuthenticatedAs($user);
    }

    public function test_scan_presensi_ecard(): void
    {
        $this->periodeTerbuka();
        $pembina = $this->pembina();
        $eskul = $this->eskul('Futsal', $pembina);
        $siswa = $this->siswa('10001');
        $reg = $this->registrasi($siswa, 'APPROVED');
        $reg->update(['verify_token' => bin2hex(random_bytes(32))]);

        // Halaman scan bisa dibuka pembina pemilik.
        $this->actingAs($pembina)->get("/pembina/eskul/{$eskul->id}/scan")->assertOk();

        // Scan QR (URL verify) tercatat HADIR.
        $this->actingAs($pembina)
            ->postJson("/pembina/eskul/{$eskul->id}/scan", [
                'kode' => route('verify.show', $reg->fresh()->verify_token),
                'tanggal' => today()->toDateString(),
            ])
            ->assertOk()
            ->assertJson(['ok' => true]);
        $this->assertDatabaseHas('kehadiran', [
            'eskul_id' => $eskul->id,
            'registration_id' => $reg->id,
            'status' => 'HADIR',
        ]);

        // Scan ulang tetap ok (idempoten).
        $this->actingAs($pembina)
            ->postJson("/pembina/eskul/{$eskul->id}/scan", [
                'kode' => $reg->fresh()->kode(),
                'tanggal' => today()->toDateString(),
            ])
            ->assertOk()
            ->assertJson(['ok' => true, 'sudah' => true]);

        // Eskul lain ditolak.
        $eskulLain = $this->eskul('Basket', $this->pembina());
        $this->actingAs($pembina)
            ->postJson("/pembina/eskul/{$eskulLain->id}/scan", [
                'kode' => $reg->fresh()->kode(),
                'tanggal' => today()->toDateString(),
            ])
            ->assertForbidden();

        // Kartu dicabut ditolak.
        $reg->update(['card_revoked_at' => now()]);
        $this->actingAs($pembina)
            ->postJson("/pembina/eskul/{$eskul->id}/scan", [
                'kode' => $reg->fresh()->kode(),
                'tanggal' => today()->toDateString(),
            ])
            ->assertStatus(422);
    }
}
