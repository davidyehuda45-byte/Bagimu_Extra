<?php

namespace Tests\Feature;

use App\Support\StudentFileReader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DptFormatImportTest extends TestCase
{
    use RefreshDatabase;

    private function dptFile(array $rows): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'dpt_').'.xlsx';
        $ss = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $ss->getActiveSheet()->fromArray($rows, null, 'A1', true);
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($ss))->save($tmp);
        $ss->disconnectWorksheets();

        return $tmp;
    }

    public function test_format_dpt_username_fullname_group_terbaca(): void
    {
        $tmp = $this->dptFile([
            ['username', 'fullName', 'userType', 'group', 'defaultPassword'],
            ['1012011', 'Agusta Kurwok', 'STUDENT', 'X RPL', 'password123'],
            ['1012012', 'Artalita Septiani', 'STUDENT', 'X Kuliner 1', 'password123'],
        ]);

        $hasil = StudentFileReader::readFileDetailed($tmp, 'import-dpt-X-RPL.xlsx', null);

        $this->assertCount(2, $hasil['records']);
        $this->assertSame('1012011', $hasil['records'][0]['nis']);
        $this->assertSame('Agusta Kurwok', $hasil['records'][0]['nama']);
        $this->assertSame('10 RPL', $hasil['records'][0]['kelas']);
        $this->assertSame('RPL', $hasil['records'][0]['jurusan']);
        $this->assertSame('KULINER', $hasil['records'][1]['jurusan']);
        @unlink($tmp);
    }

    public function test_baris_guru_dan_staf_dilewati_tanpa_error(): void
    {
        $tmp = $this->dptFile([
            ['username', 'fullName', 'userType', 'group', 'defaultPassword'],
            ['daniel21', 'Daniel Setyo', 'TEACHER', 'Dewan Guru', 'password123'],
            ['tu01', 'Staf TU', 'STAFF', 'Staf Tata Usaha', 'password123'],
            ['1012013', 'Ayu Cristiani', 'STUDENT', 'X TP', 'password123'],
        ]);

        $hasil = StudentFileReader::readFileDetailed($tmp, 'campuran.xlsx', null);

        $this->assertCount(1, $hasil['records']);
        $this->assertSame('1012013', $hasil['records'][0]['nis']);
        $this->assertSame('TP', $hasil['records'][0]['jurusan']);
        $this->assertSame(2, $hasil['non_siswa']);
        $this->assertSame(0, $hasil['skipped']);
        @unlink($tmp);
    }

    public function test_kelas_dkv1_tanpa_spasi_terdeteksi_jurusannya(): void
    {
        $tmp = $this->dptFile([
            ['username', 'fullName', 'userType', 'group', 'defaultPassword'],
            ['1033001', 'Siswa DKV', 'STUDENT', 'XII DKV1', 'password123'],
        ]);

        $hasil = StudentFileReader::readFileDetailed($tmp, 'xii.xlsx', null);

        $this->assertCount(1, $hasil['records']);
        $this->assertSame('DKV', $hasil['records'][0]['jurusan']);
        @unlink($tmp);
    }
}
