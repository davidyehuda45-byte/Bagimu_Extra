<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentKeluar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KenaikanPerRombelFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => Role::ADMIN]);
    }

    public function test_kenaikan_kelas_per_rombel_with_checklist(): void
    {
        $admin = $this->admin();

        SchoolClass::create(['nama' => '10 RPL 1', 'tingkat' => 10, 'jurusan' => 'RPL']);

        $s1 = Student::create(['nis' => '101', 'nama' => 'Siswa Naik', 'kelas' => '10 RPL 1', 'jurusan' => 'RPL', 'status' => 'AKTIF']);
        $s2 = Student::create(['nis' => '102', 'nama' => 'Siswa Tinggal', 'kelas' => '10 RPL 1', 'jurusan' => 'RPL', 'status' => 'AKTIF']);
        $s3 = Student::create(['nis' => '103', 'nama' => 'Siswa Keluar', 'kelas' => '10 RPL 1', 'jurusan' => 'RPL', 'status' => 'AKTIF']);

        // Admin selects 10 RPL 1, checks s1 to advance, leaves s2 unchecked, and marks s3 to be expelled
        $res = $this->actingAs($admin)->post(route('admin.kenaikan.store'), [
            'kelas_asal' => '10 RPL 1',
            'kelas_tujuan' => '11 RPL 1',
            'siswa_ids' => [$s1->id, $s2->id, $s3->id],
            'naik_siswa_ids' => [$s1->id], // only s1 is checked
            'keluar_siswa_ids' => [$s3->id], // s3 is marked keluar
            'alasan_keluar' => [$s3->id => 'Pindah domisili'],
        ]);

        $res->assertRedirect();

        // s1 naik ke 11 RPL 1
        $this->assertEquals('11 RPL 1', $s1->fresh()->kelas);
        $this->assertEquals('AKTIF', $s1->fresh()->status);

        // s2 tinggal kelas di 10 RPL 1
        $this->assertEquals('10 RPL 1', $s2->fresh()->kelas);
        $this->assertEquals('AKTIF', $s2->fresh()->status);

        // s3 keluar dan diarsipkan ke students_keluar
        $this->assertEquals('KELUAR', $s3->fresh()->status);
        $this->assertDatabaseHas('students_keluar', [
            'student_id' => $s3->id,
            'nis' => '103',
            'nama' => 'Siswa Keluar',
            'kelas_terakhir' => '10 RPL 1',
            'status_keluar' => 'KELUAR',
            'alasan' => 'Pindah domisili',
        ]);
    }

    public function test_keluarkan_siswa_individual_and_view_archive(): void
    {
        $admin = $this->admin();

        $student = Student::create([
            'nis' => '999',
            'nama' => 'Siswa Mutasi',
            'kelas' => '11 DKV 1',
            'jurusan' => 'DKV',
            'status' => 'AKTIF',
        ]);

        $res = $this->actingAs($admin)->post(route('admin.kenaikan.keluarkan'), [
            'student_id' => $student->id,
            'status_keluar' => 'MUTASI',
            'alasan' => 'Pindah ke SMKN 1 Surabaya',
        ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('students_keluar', [
            'nis' => '999',
            'nama' => 'Siswa Mutasi',
            'status_keluar' => 'MUTASI',
        ]);

        $resArsip = $this->actingAs($admin)->get(route('admin.kenaikan.arsip-keluar'));
        $resArsip->assertOk();
        $resArsip->assertSee('Siswa Mutasi');
        $resArsip->assertSee('MUTASI');
        $resArsip->assertSee('Pindah ke SMKN 1 Surabaya');
    }
}
