<?php

namespace App\Console\Commands;

use App\Models\AdminLog;
use App\Models\Student;
use App\Support\StudentFileReader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Impor massal data siswa dari file-file DPT per kelas.
 *
 * Contoh:
 *   php artisan app:import-dpt "C:\DavidYehudaSurbakti\dataprojek" --kelas=X --dry-run
 *   php artisan app:import-dpt "C:\DavidYehudaSurbakti\dataprojek" --kelas=X
 *
 * --kelas=X  : hanya file import-dpt-X-*.xlsx (kelas 10).
 * --dry-run  : simulasi parsing tanpa menyimpan.
 */
class ImportDptCommand extends Command
{
    protected $signature = 'app:import-dpt
        {folder : Folder berisi file import-dpt-*.xlsx}
        {--kelas= : Filter prefix kelas dari nama file (mis. X, XI, XII)}
        {--dry-run : Simulasi tanpa menyimpan ke database}';

    protected $description = 'Impor massal siswa dari file DPT per kelas (format username/fullName/group)';

    public function handle(): int
    {
        $folder = (string) $this->argument('folder');
        $kelas = $this->option('kelas');
        $dryRun = (bool) $this->option('dry-run');

        if (! is_dir($folder)) {
            $this->error("Folder tidak ditemukan: {$folder}");

            return self::FAILURE;
        }

        $pattern = $kelas !== null && $kelas !== ''
            ? "import-dpt-{$kelas}-*.xlsx"
            : 'import-dpt-*.xlsx';

        $files = glob(rtrim($folder, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$pattern) ?: [];
        // File guru/staf tidak pernah diimport sebagai siswa.
        $files = array_values(array_filter($files, function ($f) {
            $nama = strtolower(basename((string) $f));

            return ! str_contains($nama, 'guru') && ! str_contains($nama, 'master');
        }));

        if (empty($files)) {
            $this->error("Tidak ada file cocok pola {$pattern} di {$folder}");

            return self::FAILURE;
        }

        sort($files);
        $this->info(($dryRun ? '[SIMULASI] ' : '').count($files).' file akan diproses.');

        $baru = 0;
        $diperbarui = 0;
        $gagal = 0;
        $dilewati = 0;
        $nonSiswa = 0;
        $fileGagal = [];

        DB::beginTransaction();
        try {
            foreach ($files as $file) {
                $nama = basename($file);
                try {
                    $hasil = StudentFileReader::readFileDetailed($file, $nama, null);
                } catch (\RuntimeException $e) {
                    $this->error("{$nama}: GAGAL dibaca — {$e->getMessage()}");
                    $fileGagal[] = $nama;
                    continue;
                }

                $b = 0;
                $u = 0;
                $g = 0;
                foreach ($hasil['records'] as $item) {
                    $exists = Student::where('nis', $item['nis'])->exists();
                    try {
                        Student::updateOrCreate(
                            ['nis' => $item['nis']],
                            [
                                'nisn' => $item['nisn'],
                                'nama' => $item['nama'],
                                'kelas' => $item['kelas'],
                                'jurusan' => $item['jurusan'],
                                'gender' => $item['gender'],
                                'no_absen' => $item['no_absen'],
                            ]
                        );
                        $exists ? $u++ : $b++;
                    } catch (\Throwable $e) {
                        $g++;
                    }
                }

                $baru += $b;
                $diperbarui += $u;
                $gagal += $g;
                $dilewati += $hasil['skipped'];
                $nonSiswa += $hasil['non_siswa'] ?? 0;
                $this->line("{$nama}: {$b} baru, {$u} diperbarui, {$g} gagal, {$hasil['skipped']} dilewati");
            }

            if ($dryRun) {
                DB::rollBack();
                $this->warn('SIMULASI selesai — tidak ada data disimpan (rollback).');
            } else {
                DB::commit();
                AdminLog::record('student.import.dpt', 'student', null, "Impor DPT massal: {$baru} baru, {$diperbarui} diperbarui, {$gagal} gagal");
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        $this->info("TOTAL: {$baru} baru, {$diperbarui} diperbarui, {$gagal} gagal, {$dilewati} baris dilewati, {$nonSiswa} non-siswa dilewati.");
        if (! empty($fileGagal)) {
            $this->error('File gagal total: '.implode(', ', $fileGagal));

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
