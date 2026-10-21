<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Eskul;
use App\Models\Periode;
use App\Enums\PeriodeStatus;
use App\Models\Registration;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WaliKelasFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function siapkanData(): array
    {
        $periode = Periode::create(['periode' => '2026/2027', 'status' => PeriodeStatus::DIBUKA]);
        $eskul = Eskul::create(['name' => 'Robotik', 'aktif' => true]);

        $waliUser = User::factory()->create([
            'name' => 'Pak Budi Hartono, S.Pd',
            'username' => 'walibudi',
            'email' => 'budi@sekolah.sch.id',
            'role' => Role::WALI_KELAS,
            'password' => bcrypt('password123'),
        ]);

        $kelas = SchoolClass::create([
            'nama' => '10 RPL 1',
            'tingkat' => 10,
            'jurusan' => 'RPL',
            'wali_kelas_id' => $waliUser->id,
            'wali_kelas_nama' => $waliUser->name,
        ]);

        $s1 = Student::create([
            'nis' => '10001',
            'nama' => 'Ahmad Santoso',
            'kelas' => '10 RPL 1',
            'jurusan' => 'RPL',
            'status' => 'AKTIF',
        ]);
        $u1 = User::factory()->create(['username' => '10001', 'role' => Role::STUDENT]);
        $s1->update(['user_id' => $u1->id]);

        $reg1 = Registration::create([
            'user_id' => $u1->id,
            'periode' => '2026/2027',
            'kelas' => '10 RPL 1',
            'jurusan' => 'RPL',
            'nis' => '10001',
            'usia' => 16,
            'gender' => \App\Enums\Gender::LAKI_LAKI,
            'eskul_utama' => 'Robotik',
            'status' => \App\Enums\Status::APPROVED,
            'nilai' => 88,
            'predikat' => 'A',
            'catatan_nilai' => 'Sangat aktif dan berbakat.',
        ]);

        return [$waliUser, $kelas, $s1, $reg1];
    }

    public function test_wali_kelas_can_login_via_pembina_login(): void
    {
        [$waliUser] = $this->siapkanData();

        $response = $this->post('/pembina/login', [
            'login' => 'walibudi',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/wali-kelas');
        $this->assertAuthenticatedAs($waliUser);
    }

    public function test_wali_kelas_can_access_dashboard_and_see_class_metrics(): void
    {
        [$waliUser, $kelas] = $this->siapkanData();

        $response = $this->actingAs($waliUser)->get(route('wali-kelas.dashboard'));
        $response->assertOk();
        $response->assertSee('Rombel: 10 RPL 1');
        $response->assertSee('Pak Budi Hartono, S.Pd');
        $response->assertSee('Ahmad Santoso');
        $response->assertSee('Robotik');
    }

    public function test_wali_kelas_can_view_student_directory(): void
    {
        [$waliUser] = $this->siapkanData();

        $response = $this->actingAs($waliUser)->get(route('wali-kelas.siswa'));
        $response->assertOk();
        $response->assertSee('10001');
        $response->assertSee('Ahmad Santoso');
        $response->assertSee('Robotik');
    }

    public function test_wali_kelas_can_view_and_print_report_card_grades(): void
    {
        [$waliUser] = $this->siapkanData();

        $resNilai = $this->actingAs($waliUser)->get(route('wali-kelas.nilai'));
        $resNilai->assertOk();
        $resNilai->assertSee('88');
        $resNilai->assertSee('A');
        $resNilai->assertSee('Sangat aktif dan berbakat.');

        $resCetak = $this->actingAs($waliUser)->get(route('wali-kelas.cetak-nilai'));
        $resCetak->assertOk();
        $resCetak->assertSee('DAFTAR NILAI KEGIATAN EKSTRAKURIKULER');
        $resCetak->assertSee('Ahmad Santoso');
        $resCetak->assertSee('88');
    }
}
