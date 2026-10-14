<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class StudentMultiFormatImportTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => Role::ADMIN]);
    }

    public function test_import_berkas_excel_xlsx(): void
    {
        $admin = $this->admin();

        // Buat file Excel .xlsx di memori/temp
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['nis', 'nama', 'kelas', 'jurusan', 'gender', 'nisn'],
            ['50001', 'Ahmad Dani', '10 RPL 1', 'RPL', 'L', '0081234567'],
            ['50002', 'Siti Aminah', 'X DKV 2', 'DKV', 'P', '0089876543'],
        ]);

        $tempPath = tempnam(sys_get_temp_dir(), 'test_excel_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $file = new UploadedFile($tempPath, 'data_siswa.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $response = $this->actingAs($admin)->post('/admin/students/import', [
            'file' => $file,
        ]);

        $response->assertRedirect('/admin/students');
        $this->assertDatabaseHas('students', [
            'nis' => '50001',
            'nama' => 'Ahmad Dani',
            'kelas' => '10 RPL 1',
            'jurusan' => 'RPL',
        ]);
        $this->assertDatabaseHas('students', [
            'nis' => '50002',
            'nama' => 'Siti Aminah',
            'kelas' => '10 DKV 2',
            'jurusan' => 'DKV',
        ]);

        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }
    }

    public function test_import_berkas_csv_titik_koma_dan_koma(): void
    {
        $admin = $this->admin();

        $csvContent = "nis;nama;kelas;jurusan;gender\n60001;Rudi Tabuti;11 RPL 1;RPL;L\n60002;Dewi Sandra;10 KULINER 1;KULINER;P\n";
        $file = UploadedFile::fake()->createWithContent('siswa.csv', $csvContent);

        $response = $this->actingAs($admin)->post('/admin/students/import', [
            'file' => $file,
        ]);

        $response->assertRedirect('/admin/students');
        $this->assertDatabaseHas('students', ['nis' => '60001', 'nama' => 'Rudi Tabuti']);
        $this->assertDatabaseHas('students', ['nis' => '60002', 'nama' => 'Dewi Sandra']);
    }

    public function test_halaman_direktori_siswa_memuat_tombol_input_multi_format(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->get('/admin/students');
        $response->assertOk();
        $response->assertSee('Impor Data Siswa');
        $response->assertSee('direct-student-file-input');
        $response->assertSee('.xlsx');
        $response->assertSee('.pdf');
    }
}

