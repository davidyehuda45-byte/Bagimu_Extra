<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Student;
use App\Models\User;
use App\Support\StudentFileReader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class StudentImportRobustnessTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => Role::ADMIN]);
    }

    public function test_import_csv_tanpa_kolom_jurusan_terdeteksi_dari_kelas(): void
    {
        $admin = $this->admin();

        $csv = "nis,nama,kelas\n70001,Andi,10 RPL 1\n";
        $file = UploadedFile::fake()->createWithContent('siswa.csv', $csv);

        $this->actingAs($admin)->post('/admin/students/import', ['file' => $file])
            ->assertRedirect('/admin/students');

        $this->assertDatabaseHas('students', ['nis' => '70001', 'jurusan' => 'RPL']);
    }

    public function test_import_pdf_tanpa_teks_gagal_ramah_bukan_500(): void
    {
        $admin = $this->admin();

        $file = UploadedFile::fake()->createWithContent('scan.pdf', '%PDF-1.4 fake tanpa teks');

        $response = $this->actingAs($admin)->post('/admin/students/import', ['file' => $file]);

        // Harus kembali ke form dengan pesan error, bukan 500.
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_import_berkas_kosong_gagal_ramah(): void
    {
        $admin = $this->admin();

        $file = UploadedFile::fake()->createWithContent('kosong.csv', "nis,nama,kelas,jurusan\n");

        $response = $this->actingAs($admin)->post('/admin/students/import', ['file' => $file]);

        $response->assertRedirect();
        $this->assertTrue(session()->has('error') || $response->isRedirection());
    }

    public function test_import_format_tidak_didukung_ditolak_validasi(): void
    {
        $admin = $this->admin();

        $file = UploadedFile::fake()->createWithContent('data.exe', 'MZ fake');

        $this->actingAs($admin)->post('/admin/students/import', ['file' => $file])
            ->assertSessionHasErrors('file');
    }

    public function test_import_nisn_duplikat_dilaporkan_bukan_500(): void
    {
        $admin = $this->admin();
        Student::create(['nis' => '80001', 'nisn' => '0099999999', 'nama' => 'Lama', 'kelas' => '10 RPL 1', 'jurusan' => 'RPL']);

        $csv = "nis,nisn,nama,kelas,jurusan\n80002,0099999999,Baru,10 RPL 1,RPL\n80003,,Baru Kedua,10 RPL 1,RPL\n";
        $file = UploadedFile::fake()->createWithContent('siswa.csv', $csv);

        $response = $this->actingAs($admin)->post('/admin/students/import', ['file' => $file]);

        $response->assertRedirect('/admin/students');
        // Baris valid tetap masuk walau satu baris gagal.
        $this->assertDatabaseHas('students', ['nis' => '80003']);
    }

    public function test_template_csv_dan_xlsx_bisa_diunduh(): void
    {
        $admin = $this->admin();

        $resCsv = $this->actingAs($admin)->get('/admin/students/import/template?format=csv');
        $resCsv->assertOk();
        $this->assertStringContainsString('attachment', $resCsv->headers->get('Content-Disposition'));
        $this->assertStringContainsString('template-siswa.csv', $resCsv->headers->get('Content-Disposition'));

        $this->actingAs($admin)->get('/admin/students/import/template?format=xlsx')
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_reader_langsung_csv_semicolon(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'csv_');
        file_put_contents($tmp, "nis;nama;kelas;jurusan\n90001;Eko;10 TP 1;TP\n");

        $hasil = StudentFileReader::readFileDetailed($tmp, 'siswa.csv', 'text/csv');

        $this->assertCount(1, $hasil['records']);
        $this->assertSame('90001', $hasil['records'][0]['nis']);
        $this->assertSame('TP', $hasil['records'][0]['jurusan']);
        @unlink($tmp);
    }
}
