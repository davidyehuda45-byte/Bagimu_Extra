<?php

namespace Tests\Feature;

use App\Enums\Jurusan;
use App\Enums\PeriodeStatus;
use App\Enums\Role;
use App\Models\Periode;
use App\Models\Registration;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentDirectoryFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => Role::ADMIN]);
    }

    public function test_index_page_loads_and_renders_correctly(): void
    {
        $admin = $this->admin();
        Student::create([
            'nis' => '1001',
            'nama' => 'Budi Santoso',
            'kelas' => '10 RPL 1',
            'jurusan' => 'RPL',
            'status' => 'AKTIF',
        ]);

        $res = $this->actingAs($admin)->get(route('admin.students.index'));
        $res->assertOk();
        $res->assertSee('Generate Semua Token');
        $res->assertSee('Budi Santoso');
        $res->assertSee('10 RPL 1');
        // Pastikan tidak ada duplikasi teks jurusan seperti "RPL (RPL"
        $res->assertDontSee('RPL (RPL');
    }

    public function test_index_ajax_returns_json_with_rows_and_pagination(): void
    {
        $admin = $this->admin();
        Student::create([
            'nis' => '1001',
            'nama' => 'Budi Santoso',
            'kelas' => '10 RPL 1',
            'jurusan' => 'RPL',
            'status' => 'AKTIF',
        ]);

        $res = $this->actingAs($admin)->get(route('admin.students.index', ['kelas' => '10 RPL 1']), [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ]);

        $res->assertOk();
        $res->assertJsonStructure(['rows', 'pagination', 'total']);
        $this->assertEquals(1, $res->json('total'));
        $this->assertStringContainsString('Budi Santoso', $res->json('rows'));
    }

    public function test_filter_by_tingkat_kelas(): void
    {
        $admin = $this->admin();
        Student::create([
            'nis' => '1001',
            'nama' => 'Siswa Sepuluh',
            'kelas' => '10 RPL 1',
            'jurusan' => 'RPL',
            'status' => 'AKTIF',
        ]);
        Student::create([
            'nis' => '1002',
            'nama' => 'Siswa Sebelas',
            'kelas' => 'XI RPL 1',
            'jurusan' => 'RPL',
            'status' => 'AKTIF',
        ]);

        $res = $this->actingAs($admin)->get(route('admin.students.index', ['tingkat' => '10']));
        $res->assertOk();
        $res->assertSee('Siswa Sepuluh');
        $res->assertDontSee('Siswa Sebelas');

        $res2 = $this->actingAs($admin)->get(route('admin.students.index', ['tingkat' => '11']));
        $res2->assertOk();
        $res2->assertSee('Siswa Sebelas');
        $res2->assertDontSee('Siswa Sepuluh');
    }

    public function test_bulk_generate_token_for_untokenized_students(): void
    {
        $admin = $this->admin();
        $s1 = Student::create([
            'nis' => '1001',
            'nama' => 'Siswa Satu',
            'kelas' => '10 RPL 1',
            'jurusan' => 'RPL',
            'status' => 'AKTIF',
        ]);
        $s2 = Student::create([
            'nis' => '1002',
            'nama' => 'Siswa Dua',
            'kelas' => '10 RPL 1',
            'jurusan' => 'RPL',
            'status' => 'AKTIF',
        ]);

        $res = $this->actingAs($admin)->post(route('admin.students.bulk-token'), [
            'mode' => 'untokenized',
        ]);

        $res->assertRedirect();
        $res->assertSessionHas('status');

        $s1->refresh();
        $s2->refresh();

        $this->assertNotNull($s1->user);
        $this->assertNotNull($s1->user->login_token);
        $this->assertNotNull($s2->user);
        $this->assertNotNull($s2->user->login_token);
    }

    public function test_bulk_delete_selected_students(): void
    {
        $admin = $this->admin();
        $s1 = Student::create([
            'nis' => '1001',
            'nama' => 'Siswa Hapus 1',
            'kelas' => '10 RPL 1',
            'jurusan' => 'RPL',
            'status' => 'AKTIF',
        ]);
        $s2 = Student::create([
            'nis' => '1002',
            'nama' => 'Siswa Hapus 2',
            'kelas' => '10 RPL 1',
            'jurusan' => 'RPL',
            'status' => 'AKTIF',
        ]);
        $s3 = Student::create([
            'nis' => '1003',
            'nama' => 'Siswa Tetap',
            'kelas' => '10 RPL 1',
            'jurusan' => 'RPL',
            'status' => 'AKTIF',
        ]);

        $res = $this->actingAs($admin)->post(route('admin.students.bulk-delete'), [
            'mode' => 'selected',
            'ids' => [$s1->id, $s2->id],
        ]);

        $res->assertRedirect();
        $res->assertSessionHas('status');

        $this->assertDatabaseMissing('students', ['id' => $s1->id]);
        $this->assertDatabaseMissing('students', ['id' => $s2->id]);
        $this->assertDatabaseHas('students', ['id' => $s3->id]);
    }

    public function test_bulk_generate_token_for_selected_students(): void
    {
        $admin = $this->admin();
        $s1 = Student::create([
            'nis' => '1001',
            'nama' => 'Siswa Pilih 1',
            'kelas' => '10 RPL 1',
            'jurusan' => 'RPL',
            'status' => 'AKTIF',
        ]);
        $s2 = Student::create([
            'nis' => '1002',
            'nama' => 'Siswa Tidak Dipilih',
            'kelas' => '10 RPL 1',
            'jurusan' => 'RPL',
            'status' => 'AKTIF',
        ]);

        $res = $this->actingAs($admin)->post(route('admin.students.bulk-token'), [
            'mode' => 'selected',
            'ids' => [$s1->id],
        ]);

        $res->assertRedirect();
        $res->assertSessionHas('status');

        $s1->refresh();
        $s2->refresh();

        $this->assertNotNull($s1->user);
        $this->assertNotNull($s1->user->login_token);
        $this->assertNull($s2->user);
    }

    public function test_pagination_preserves_query_string_in_links(): void
    {
        $admin = $this->admin();
        // Buat 25 siswa di kelas 10 RPL 1 (melebihi perPage 20)
        for ($i = 1; $i <= 25; $i++) {
            Student::create([
                'nis' => '100' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'nama' => "Siswa {$i}",
                'kelas' => '10 RPL 1',
                'jurusan' => 'RPL',
                'status' => 'AKTIF',
            ]);
        }

        $res = $this->actingAs($admin)->get(route('admin.students.index', [
            'kelas' => '10 RPL 1',
            'jurusan' => 'RPL',
        ]));

        $res->assertOk();
        // Pastikan link halaman 2 membawa query string filter
        $this->assertTrue(
            str_contains($res->getContent(), 'kelas=10%20RPL%201') || str_contains($res->getContent(), 'kelas=10+RPL+1')
        );
        $res->assertSee('jurusan=RPL');
        $res->assertSee('page=2');
    }

    public function test_status_rombel_renders_and_computes_ratios(): void
    {
        $admin = $this->admin();
        Periode::create([
            'periode' => '2024-1',
            'nama' => 'Ganjil 2024/2025',
            'tahun_ajaran' => '2024/2025',
            'semester' => 'GANJIL',
            'tgl_mulai' => now()->subDay(),
            'tgl_selesai' => now()->addMonth(),
            'status' => PeriodeStatus::DIBUKA,
        ]);

        $user = User::factory()->create(['role' => Role::STUDENT, 'username' => '1001']);
        Student::create([
            'user_id' => $user->id,
            'nis' => '1001',
            'nama' => 'Budi Santoso',
            'kelas' => '10 RPL 1',
            'jurusan' => 'RPL',
            'status' => 'AKTIF',
        ]);
        Student::create([
            'nis' => '1002',
            'nama' => 'Ani Wijaya',
            'kelas' => '10 RPL 1',
            'jurusan' => 'RPL',
            'status' => 'AKTIF',
        ]);

        Registration::create([
            'user_id' => $user->id,
            'periode' => '2024-1',
            'kelas' => '10 RPL 1',
            'nis' => '1001',
            'nama_wali_kelas' => 'Guru A',
            'jurusan' => Jurusan::RPL,
            'usia' => 16,
            'gender' => \App\Enums\Gender::LAKI_LAKI,
            'whatsapp' => '08123456789',
            'eskul_utama' => 'Robotik',
            'status' => \App\Enums\Status::PENDING,
        ]);

        $res = $this->actingAs($admin)->get(route('admin.students.status-rombel'));
        $res->assertOk();
        $res->assertSee('Status Pendaftaran Per Rombel');
        $res->assertSee('10 RPL 1');
        $res->assertSee('50%');
        $res->assertSee('1 siswa belum memilih');
    }

    public function test_generate_token_siswa(): void
    {
        $admin = $this->admin();
        $s1 = Student::create([
            'nis' => '1001',
            'nama' => 'Budi Santoso',
            'kelas' => '10 RPL 1',
            'jurusan' => 'RPL',
            'status' => 'AKTIF',
        ]);

        $res = $this->actingAs($admin)->post(route('admin.students.token', $s1));
        $res->assertRedirect();

        $s1->refresh();
        $this->assertNotNull($s1->user);
        $this->assertNotNull($s1->user->login_token);
        $this->assertEquals(8, strlen($s1->user->login_token));
    }

    public function test_export_csv_and_xlsx(): void
    {
        $admin = $this->admin();
        Student::create([
            'nis' => '1001',
            'nama' => 'Budi Santoso',
            'kelas' => '10 RPL 1',
            'jurusan' => 'RPL',
            'status' => 'AKTIF',
        ]);

        $csvRes = $this->actingAs($admin)->get(route('admin.students.export', ['format' => 'csv']));
        $csvRes->assertOk();
        $this->assertStringContainsString('text/csv', $csvRes->headers->get('content-type'));

        $xlsxRes = $this->actingAs($admin)->get(route('admin.students.export', ['format' => 'xlsx']));
        $xlsxRes->assertOk();
        $this->assertStringContainsString('spreadsheetml', $xlsxRes->headers->get('content-type'));
    }

    public function test_filter_by_eskul_status(): void
    {
        $admin = $this->admin();
        Periode::create([
            'periode' => '2024-1',
            'nama' => 'Ganjil 2024/2025',
            'tahun_ajaran' => '2024/2025',
            'semester' => 'GANJIL',
            'tgl_mulai' => now()->subDay(),
            'tgl_selesai' => now()->addMonth(),
            'status' => PeriodeStatus::DIBUKA,
        ]);

        $user = User::factory()->create(['role' => Role::STUDENT, 'username' => '1001']);
        Student::create([
            'user_id' => $user->id,
            'nis' => '1001',
            'nama' => 'Siswa Terdaftar',
            'kelas' => '10 RPL 1',
            'jurusan' => 'RPL',
            'status' => 'AKTIF',
        ]);
        Student::create([
            'nis' => '1002',
            'nama' => 'Siswa Belum Terdaftar',
            'kelas' => '10 RPL 1',
            'jurusan' => 'RPL',
            'status' => 'AKTIF',
        ]);

        Registration::create([
            'user_id' => $user->id,
            'periode' => '2024-1',
            'kelas' => '10 RPL 1',
            'nis' => '1001',
            'nama_wali_kelas' => 'Guru A',
            'jurusan' => Jurusan::RPL,
            'usia' => 16,
            'gender' => \App\Enums\Gender::LAKI_LAKI,
            'whatsapp' => '08123456789',
            'eskul_utama' => 'Robotik',
            'status' => \App\Enums\Status::PENDING,
        ]);

        $res1 = $this->actingAs($admin)->get(route('admin.students.index', ['eskul_status' => 'terdaftar']));
        $res1->assertOk();
        $res1->assertSee('Siswa Terdaftar');
        $res1->assertDontSee('Siswa Belum Terdaftar');

        $res2 = $this->actingAs($admin)->get(route('admin.students.index', ['eskul_status' => 'belum']));
        $res2->assertOk();
        $res2->assertSee('Siswa Belum Terdaftar');
        $res2->assertDontSee('Siswa Terdaftar');
    }
}
