<?php

namespace Tests\Feature;

use App\Enums\PeriodeStatus;
use App\Enums\Role;
use App\Enums\Status;
use App\Models\Eskul;
use App\Models\Izin;
use App\Models\InAppNotification;
use App\Models\PendaftaranWindow;
use App\Models\Periode;
use App\Models\Registration;
use App\Models\Student;
use App\Models\User;
use App\Support\Notifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FiturTambahanTest extends TestCase
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
            'nis' => '10001', 'nama' => 'Siswa Tes', 'kelas' => 'X RPL 1',
            'jurusan' => 'RPL', 'status' => 'AKTIF',
        ]);
        $siswa = User::factory()->create(['username' => '10001', 'role' => Role::STUDENT]);

        $reg = Registration::create([
            'user_id' => $siswa->id, 'periode' => '2026/2027', 'kelas' => 'X RPL 1',
            'nis' => '10001', 'jurusan' => 'RPL', 'usia' => 16, 'gender' => 'LAKI_LAKI',
            'eskul_utama' => 'Futsal', 'status' => 'PENDING',
        ]);

        return [$siswa, $reg, $eskul];
    }

    public function test_daftar_mengirim_notifikasi(): void
    {
        Periode::create(['periode' => '2026/2027', 'status' => PeriodeStatus::DIBUKA]);
        PendaftaranWindow::create(['periode' => '2026/2027', 'buka' => true]);
        Eskul::create(['name' => 'Futsal', 'aktif' => true]);

        Student::create([
            'nis' => '70001', 'nama' => 'Siswa Baru', 'kelas' => '10 RPL 1',
            'jurusan' => 'RPL', 'status' => 'AKTIF',
        ]);
        $siswa = User::factory()->create(['username' => '70001', 'role' => Role::STUDENT]);

        $this->actingAs($siswa)->post('/siswa/daftar', [
            'kelas' => '10 RPL 1',
            'no_absen' => 5,
            'nama_wali_kelas' => 'Bu Wali',
            'jurusan' => 'RPL',
            'usia' => 16,
            'gender' => 'LAKI_LAKI',
            'whatsapp' => '081234567890',
            'eskul_utama' => 'Futsal',
            'alasan' => 'Ingin sehat.',
        ])->assertRedirect('/siswa/sukses');

        $this->assertDatabaseHas('inapp_notifications', [
            'user_id' => $siswa->id,
            'judul' => 'Pendaftaran berhasil',
        ]);
    }

    public function test_export_pendaftar_csv(): void
    {
        $this->siapkan();

        $res = $this->actingAs($this->admin())->get('/admin/registrations/export?periode=2026/2027');
        $res->assertOk();
        $this->assertStringContainsString('text/csv', $res->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', $res->headers->get('Content-Disposition'));
        $this->assertStringContainsString('Nama', $res->streamedContent());
        $this->assertStringContainsString('10001', $res->streamedContent());
    }

    public function test_export_laporan_csv(): void
    {
        $this->siapkan();

        $res = $this->actingAs($this->admin())->get('/admin/laporan/export?periode=2026/2027');
        $res->assertOk();
        $this->assertStringContainsString('Futsal', $res->streamedContent());
    }

    public function test_landing_memuat_jadwal(): void
    {
        [$siswa, $reg, $eskul] = $this->siapkan();
        $eskul->jadwals()->create([
            'hari' => 'SELASA', 'jam_mulai' => '15:00', 'jam_selesai' => '17:00', 'tempat' => 'Lapangan',
        ]);

        $this->get('/')->assertOk()->assertSee('Jadwal Latihan Mingguan')->assertSee('Selasa');
    }

    public function test_dashboard_memuat_grafik(): void
    {
        [$siswa, $reg, $eskul] = $this->siapkan();

        $this->actingAs($this->admin())->get('/admin')->assertOk()->assertSee('Distribusi Per Eskul');

        $pembina = User::factory()->create(['role' => Role::PEMBINA]);
        $eskul->update(['pembina_id' => $pembina->id]);

        $this->actingAs($pembina)->get('/pembina')->assertOk()->assertSee('Eskul Saya');
    }

    public function test_pembina_export_rekap_csv_dan_excel(): void
    {
        [$siswa, $reg, $eskul] = $this->siapkan();
        $siswa->update(['name' => 'Siswa Tes']);
        $reg->update(['status' => Status::APPROVED, 'nilai' => 88, 'predikat' => 'A']);

        $pembina = User::factory()->create(['role' => Role::PEMBINA]);
        $eskul->update(['pembina_id' => $pembina->id]);

        $otherPembina = User::factory()->create(['role' => Role::PEMBINA]);

        // Unauthorized pembina gets 403
        $this->actingAs($otherPembina)->get("/pembina/eskul/{$eskul->id}/export?format=csv")->assertForbidden();

        // Authorized pembina can export CSV
        $resCsv = $this->actingAs($pembina)->get("/pembina/eskul/{$eskul->id}/export?format=csv");
        $resCsv->assertOk();
        $this->assertStringContainsString('text/csv', $resCsv->headers->get('Content-Type'));
        $this->assertStringContainsString('Siswa Tes', $resCsv->streamedContent());
        $this->assertStringContainsString('10001', $resCsv->streamedContent());

        // Authorized pembina can export Excel
        $resXls = $this->actingAs($pembina)->get("/pembina/eskul/{$eskul->id}/export?format=excel");
        $resXls->assertOk();
        $this->assertStringContainsString('application/vnd.ms-excel', $resXls->headers->get('Content-Type'));
        $this->assertStringContainsString('Siswa Tes', $resXls->streamedContent());
    }

    public function test_admin_bisa_mengubah_jadwal_eskul(): void
    {
        $admin = $this->admin();
        $eskul = Eskul::create(['name' => 'Futsal', 'aktif' => true]);
        $jadwal = $eskul->jadwals()->create([
            'hari' => 'SENIN',
            'jam_mulai' => '15:00',
            'jam_selesai' => '17:00',
            'tempat' => 'Lapangan A',
        ]);

        $res = $this->actingAs($admin)->put("/admin/jadwals/{$jadwal->id}", [
            'hari' => 'SELASA',
            'jam_mulai' => '16:00',
            'jam_selesai' => '18:00',
            'tempat' => 'Lapangan B Baru',
        ]);

        $res->assertRedirect("/admin/eskuls/{$eskul->id}");
        $this->assertDatabaseHas('eskul_jadwals', [
            'id' => $jadwal->id,
            'hari' => 'SELASA',
            'jam_mulai' => '16:00',
            'jam_selesai' => '18:00',
            'tempat' => 'Lapangan B Baru',
        ]);
    }

    public function test_pembina_bisa_mengubah_jadwal_eskul_miliknya(): void
    {
        $pembina = User::factory()->create(['role' => Role::PEMBINA]);
        $otherPembina = User::factory()->create(['role' => Role::PEMBINA]);
        $eskul = Eskul::create(['name' => 'Futsal', 'aktif' => true, 'pembina_id' => $pembina->id]);
        $jadwal = $eskul->jadwals()->create([
            'hari' => 'RABU',
            'jam_mulai' => '14:00',
            'jam_selesai' => '16:00',
            'tempat' => 'Aula Utama',
        ]);

        // Pembina lain ditolak
        $this->actingAs($otherPembina)->put("/pembina/jadwals/{$jadwal->id}", [
            'hari' => 'KAMIS',
            'jam_mulai' => '15:00',
            'jam_selesai' => '17:00',
            'tempat' => 'Aula 2',
        ])->assertForbidden();

        // Pembina pemilik berhasil
        $res = $this->actingAs($pembina)->put("/pembina/jadwals/{$jadwal->id}", [
            'hari' => 'KAMIS',
            'jam_mulai' => '15:00',
            'jam_selesai' => '17:00',
            'tempat' => 'Aula 2',
        ]);

        $res->assertRedirect("/pembina/eskul/{$eskul->id}");
        $this->assertDatabaseHas('eskul_jadwals', [
            'id' => $jadwal->id,
            'hari' => 'KAMIS',
            'jam_mulai' => '15:00',
            'jam_selesai' => '17:00',
            'tempat' => 'Aula 2',
        ]);
    }
}
