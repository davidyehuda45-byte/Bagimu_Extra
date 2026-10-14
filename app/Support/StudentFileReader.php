<?php

namespace App\Support;

use App\Enums\Gender;
use App\Enums\Jurusan;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use RuntimeException;
use Smalot\PdfParser\Parser as PdfParser;

class StudentFileReader
{
    /**
     * Membaca dan mengekstrak daftar siswa dari berbagai jenis file:
     * - Excel (.xlsx, .xls, .ods)
     * - CSV & Text (.csv, .txt, .tsv)
     * - Dokumen PDF (.pdf)
     *
     * Melempar RuntimeException dengan pesan ramah-pengguna bila berkas
     * tidak bisa dibaca (bukan diam-diam mengembalikan array kosong).
     *
     * @return array<int, array{nis: string, nisn: ?string, nama: string, kelas: string, jurusan: string, gender: ?string, no_absen: ?int}>
     *
     * @throws RuntimeException
     */
    public static function readFile(string $filePath, ?string $originalName = null, ?string $mimeType = null): array
    {
        return self::readFileDetailed($filePath, $originalName, $mimeType)['records'];
    }

    /**
     * Versi detail: mengembalikan records + info baris yang dilewati.
     *
     * @return array{records: array<int, array{nis: string, nisn: ?string, nama: string, kelas: string, jurusan: string, gender: ?string, no_absen: ?int}>, skipped: int, skippedSamples: array<int, string>}
     *
     * @throws RuntimeException
     */
    public static function readFileDetailed(string $filePath, ?string $originalName = null, ?string $mimeType = null): array
    {
        if (! is_file($filePath) || ! is_readable($filePath)) {
            throw new RuntimeException('Berkas yang diunggah tidak dapat dibaca server. Silakan coba unggah ulang.');
        }

        if (filesize($filePath) === 0) {
            throw new RuntimeException('Berkas yang diunggah kosong (0 byte). Pastikan file tidak korup.');
        }

        $ext = strtolower(pathinfo($originalName ?? $filePath, PATHINFO_EXTENSION));

        // Tebak dari mime bila nama file tanpa ekstensi (mis. tmp upload).
        if ($ext === '' && is_string($mimeType)) {
            $ext = match (true) {
                str_contains($mimeType, 'spreadsheetml') => 'xlsx',
                str_contains($mimeType, 'ms-excel') => 'xls',
                str_contains($mimeType, 'opendocument') => 'ods',
                str_contains($mimeType, 'pdf') => 'pdf',
                default => 'csv',
            };
        }

        // 1. PDF punya jalur khusus.
        if ($ext === 'pdf' || $mimeType === 'application/pdf') {
            return self::parsePdfDetailed($filePath, $originalName ?? 'dokumen.pdf');
        }

        // 2. Cek ekstensi PHP yang dibutuhkan SEBELUM mencoba membaca,
        //    agar pesannya jelas (kasus nyata: ext-zip mati di Laragon).
        if (in_array($ext, ['xlsx', 'ods'], true) && ! extension_loaded('zip')) {
            throw new RuntimeException(
                'File Excel (.'.strtoupper($ext).') tidak bisa dibaca karena ekstensi ZIP PHP belum aktif. '
                .'Aktifkan "extension=zip" di php.ini Laragon (Menu > PHP > php.ini), lalu Restart Laragon. '
                .'Sementara itu, simpan file sebagai CSV lalu impor ulang.'
            );
        }

        // 3. CSV/teks: baca dengan reader khusus + auto-deteksi delimiter
        //    (Excel Indonesia memakai ';', bukan ',').
        if (in_array($ext, ['csv', 'txt', 'tsv'], true)) {
            return self::parseCsvDetailed($filePath, $originalName ?? 'data.csv');
        }

        // 4. Excel biner: pakai reader sesuai tipe (lebih andal daripada
        //    menebak dari file tmp tanpa ekstensi).
        try {
            return self::parseSpreadsheetDetailed($filePath, $ext);
        } catch (RuntimeException $e) {
            throw $e;
        } catch (\Throwable $e) {
            // Kemungkinan file sebenarnya CSV yang dinamai .xls/.xlsx:
            // coba jalur CSV sebagai fallback terakhir.
            try {
                return self::parseCsvDetailed($filePath, $originalName ?? 'data.csv');
            } catch (RuntimeException $csvError) {
                throw new RuntimeException(
                    'File tidak bisa dibaca sebagai Excel maupun CSV. Pastikan file tidak korup dan '
                    .'merupakan .xlsx/.xls/.ods/.csv yang valid. Detail: '.$e->getMessage()
                );
            }
        }
    }

    /**
     * Membaca berkas spreadsheet (Excel .xlsx, .xls, .ods)
     */
    protected static function parseSpreadsheetDetailed(string $filePath, string $ext): array
    {
        $reader = match ($ext) {
            'xls' => IOFactory::createReader('Xls'),
            'ods' => IOFactory::createReader('Ods'),
            'xlsx' => IOFactory::createReader('Xlsx'),
            default => null,
        };

        if ($reader === null) {
            // Ekstensi tak dikenal: biarkan PhpSpreadsheet menebak dari isi.
            $spreadsheet = IOFactory::load($filePath);
        } else {
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
        }

        $sheet = $spreadsheet->getActiveSheet();
        $rawRows = $sheet->toArray(null, true, true, false);
        $spreadsheet->disconnectWorksheets();

        return self::rowsToRecords($rawRows, 'Excel');
    }

    /**
     * Pembaca CSV/teks dengan auto-deteksi delimiter (, ; \t |).
     * Tidak bergantung pada delimiter default PhpSpreadsheet (',').
     */
    protected static function parseCsvDetailed(string $filePath, string $label): array
    {
        $raw = file_get_contents($filePath);
        if ($raw === false || trim($raw) === '') {
            throw new RuntimeException("Berkas {$label} kosong atau tidak bisa dibaca.");
        }

        // Buang BOM agar header pertama bersih.
        $raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw);
        $raw = str_replace(["\r\n", "\r"], "\n", $raw);
        $lines = array_values(array_filter(array_map('trim', explode("\n", $raw)), fn ($l) => $l !== ''));

        if (empty($lines)) {
            throw new RuntimeException("Berkas {$label} kosong atau tidak ada baris data.");
        }

        // Coba reader PhpSpreadsheet dulu dengan delimiter terdeteksi…
        $delimiter = self::detectDelimiter($lines[0]);

        try {
            $reader = new CsvReader();
            $reader->setReadDataOnly(true);
            $reader->setDelimiter($delimiter);
            $reader->setEnclosure('"');
            $reader->setSheetIndex(0);
            $spreadsheet = $reader->load($filePath);
            $rawRows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
            $spreadsheet->disconnectWorksheets();

            // Jika reader menghasilkan 1 kolom padahal delimiter terdeteksi,
            // berarti tebakan salah — lanjut ke parser manual di bawah.
            if (count($rawRows) > 0 && count(array_filter($rawRows[0], fn ($v) => $v !== null && trim((string) $v) !== '')) > 1) {
                return self::rowsToRecords($rawRows, 'CSV');
            }
        } catch (\Throwable $e) {
            // lanjut ke parser manual
        }

        // …fallback parser manual per baris (toleran terhadap baris rusak).
        $firstRow = str_getcsv(array_shift($lines), $delimiter);
        $headerMap = array_map(fn ($h) => self::cleanHeader((string) $h), $firstRow);

        $records = [];
        $skipped = 0;
        $nonSiswa = 0;
        $skippedSamples = [];
        foreach ($lines as $line) {
            $row = str_getcsv($line, $delimiter);
            if (empty($row) || (count($row) === 1 && trim($row[0]) === '')) {
                $skipped++;
                continue;
            }

            $item = [];
            foreach ($headerMap as $idx => $key) {
                if ($key !== '') {
                    $item[$key] = isset($row[$idx]) ? trim((string) $row[$idx]) : '';
                }
            }

            if (self::isNonSiswa($item)) {
                $nonSiswa++;
                continue;
            }

            $record = self::normalizeRecord($item);
            if ($record !== null) {
                $records[] = $record;
            } else {
                $skipped++;
                if (count($skippedSamples) < 5) {
                    $skippedSamples[] = mb_substr($line, 0, 120);
                }
            }
        }

        return self::finish($records, $skipped, $skippedSamples, 'CSV', $nonSiswa);
    }

    /**
     * Mengubah matriks baris (hasil spreadsheet) menjadi records siswa.
     */
    protected static function rowsToRecords(array $rawRows, string $label): array
    {
        if (empty($rawRows)) {
            throw new RuntimeException("Berkas {$label} kosong atau tidak ada data.");
        }

        // Cari baris header (biasanya baris 1, atau baris pertama yang memuat kata 'nis' atau 'nama')
        $headerIndex = null;
        $headerMap = [];

        foreach ($rawRows as $idx => $row) {
            if (! is_array($row)) {
                continue;
            }
            $normalizedRow = array_map(fn ($cell) => self::cleanHeader((string) $cell), array_values($row));
            if (in_array('nis', $normalizedRow, true) || in_array('nama', $normalizedRow, true)) {
                $headerIndex = $idx;
                $headerMap = $normalizedRow;
                break;
            }
        }

        // Jika tidak ditemukan baris header eksplisit, gunakan baris pertama
        if ($headerIndex === null) {
            $first = array_values($rawRows[0] ?? []);
            $headerIndex = 0;
            $headerMap = array_map(fn ($cell) => self::cleanHeader((string) $cell), $first);
        }

        $records = [];
        $skipped = 0;
        $nonSiswa = 0;
        $skippedSamples = [];
        $dataRows = array_slice($rawRows, $headerIndex + 1);

        foreach ($dataRows as $row) {
            if (! is_array($row) || empty(array_filter(array_values($row), fn ($v) => $v !== null && trim((string) $v) !== ''))) {
                continue; // Lewati baris kosong tanpa menghitung sebagai dilewati
            }

            $row = array_values($row);
            $item = [];
            foreach ($headerMap as $colIdx => $key) {
                if ($key !== '') {
                    $item[$key] = isset($row[$colIdx]) ? trim((string) $row[$colIdx]) : '';
                }
            }

            if (self::isNonSiswa($item)) {
                $nonSiswa++;
                continue;
            }

            $record = self::normalizeRecord($item);
            if ($record !== null) {
                $records[] = $record;
            } else {
                $skipped++;
                if (count($skippedSamples) < 5) {
                    $skippedSamples[] = mb_substr(implode(' | ', array_map(fn ($v) => trim((string) $v), $row)), 0, 120);
                }
            }
        }

        return self::finish($records, $skipped, $skippedSamples, $label, $nonSiswa);
    }

    protected static function finish(array $records, int $skipped, array $skippedSamples, string $label, int $nonSiswa = 0): array
    {
        if (empty($records)) {
            $hint = 'Pastikan kolom header memuat minimal: nis (atau username DPT), nama (atau fullName), kelas (atau group), dan jurusan '
                .'(atau kelas yang mengandung jurusan, mis. "X RPL"). '
                .'Jurusan yang dikenali: RPL, KULINER, DKV, TP, TKP. Unduh template pada halaman impor sebagai contoh.';
            if ($skipped > 0) {
                $hint .= " ({$skipped} baris dilewati karena tidak valid"
                    .(! empty($skippedSamples) ? ', contoh: "'.implode('"; "', $skippedSamples).'"' : '').').';
            }
            if ($nonSiswa > 0) {
                $hint .= " ({$nonSiswa} baris guru/staf dilewati.)";
            }
            throw new RuntimeException("Berkas {$label} tidak mengandung data siswa yang valid. {$hint}");
        }

        return ['records' => $records, 'skipped' => $skipped, 'skippedSamples' => $skippedSamples, 'non_siswa' => $nonSiswa];
    }

    protected static function detectDelimiter(string $firstLine): string
    {
        $delimiters = [';', ',', "\t", '|'];
        $bestDelimiter = ',';
        $maxCount = 0;
        foreach ($delimiters as $delim) {
            $count = substr_count($firstLine, $delim);
            if ($count > $maxCount) {
                $maxCount = $count;
                $bestDelimiter = $delim;
            }
        }

        // Validasi silang dengan str_getcsv: pilih yang menghasilkan kolom terbanyak.
        $bestCols = count(str_getcsv($firstLine, $bestDelimiter));
        foreach ($delimiters as $delim) {
            $cols = count(str_getcsv($firstLine, $delim));
            if ($cols > $bestCols) {
                $bestCols = $cols;
                $bestDelimiter = $delim;
            }
        }

        return $bestDelimiter;
    }

    /**
     * Ekstraksi teks dari dokumen PDF dan penguraian baris tabel siswa
     */
    protected static function parsePdfDetailed(string $filePath, string $label): array
    {
        try {
            $parser = new PdfParser();
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();
        } catch (\Throwable $e) {
            throw new RuntimeException(
                "File {$label} tidak bisa dibaca sebagai PDF (kemungkinan korup atau terproteksi password). "
                .'Coba simpan ulang/export sebagai Excel atau CSV lalu impor ulang.'
            );
        }

        $text = str_replace(["\r\n", "\r"], "\n", (string) $text);
        $lines = array_values(array_filter(array_map('trim', explode("\n", $text)), fn ($l) => $l !== ''));

        if (empty($lines)) {
            throw new RuntimeException(
                "File {$label} tidak mengandung teks yang bisa dibaca. "
                .'Ini biasa terjadi pada PDF hasil SCAN/FOTO. Solusi: ketik ulang ke template Excel/CSV pada halaman impor, '
                .'atau gunakan PDF yang diekspor langsung dari aplikasi (bukan hasil scan).'
            );
        }

        $records = [];
        $skipped = 0;
        $jurusanPatterns = implode('|', array_map(fn ($j) => preg_quote($j->value, '/'), Jurusan::cases()));

        foreach ($lines as $line) {
            // Lewati baris header/kop surat
            $lower = strtolower($line);
            if (str_contains($lower, 'daftar') || str_contains($lower, 'halaman') || (str_contains($lower, 'nis') && str_contains($lower, 'nama'))) {
                continue;
            }

            // Jika baris dipisahkan tab, koma, atau titik koma
            if (str_contains($line, ';') || str_contains($line, ',') || str_contains($line, "\t")) {
                $delim = str_contains($line, ';') ? ';' : (str_contains($line, "\t") ? "\t" : ',');
                $parts = array_map('trim', str_getcsv($line, $delim));
                if (count($parts) >= 3) {
                    $item = self::guessColumnsFromList($parts);
                    $rec = self::normalizeRecord($item);
                    if ($rec !== null) {
                        $records[] = $rec;
                        continue;
                    }
                }
            }

            // Pola teks tabel bebas: [No] [NIS] [NISN?] [Nama Siswa...] [Kelas] [Jurusan] [Gender?]
            // Contoh: "1  30001  0081234567  Andi Pratama  10 RPL 1  RPL  L  1"
            // Atau: "30001  Andi Pratama  X RPL 2  RPL"
            if (preg_match('/(?:^|\s)(\d{4,10})\s+(.+?)\s+((?:10|11|12|X|XI|XII)\s+[A-Za-z0-9\s]+?)\s+('.$jurusanPatterns.')(?:\s+([LP]|LAKI[_-]LAKI|PEREMPUAN))?(?:\s+(\d+))?$/i', $line, $m)) {
                $rec = self::normalizeRecord([
                    'nis' => $m[1],
                    'nama' => trim($m[2]),
                    'kelas' => trim($m[3]),
                    'jurusan' => strtoupper(trim($m[4])),
                    'gender' => ! empty($m[5]) ? trim($m[5]) : null,
                    'no_absen' => ! empty($m[6]) ? (int) $m[6] : null,
                ]);

                if ($rec !== null) {
                    $records[] = $rec;
                    continue;
                }
            }

            $skipped++;
        }

        if (empty($records)) {
            throw new RuntimeException(
                "File {$label} terbaca sebagai teks, tetapi tidak ada baris data siswa yang dikenali "
                ."({$skipped} baris dilewati). Format PDF yang didukung: tabel berisi NIS, Nama, Kelas (mis. \"10 RPL 1\"), "
                .'dan Jurusan (RPL/KULINER/DKV/TP/TKP). Untuk hasil terbaik gunakan template Excel/CSV pada halaman impor.'
            );
        }

        return ['records' => $records, 'skipped' => $skipped, 'skippedSamples' => []];
    }

    /**
     * Menebak arti kolom jika formatnya baris berurutan tanpa nama kolom
     */
    protected static function guessColumnsFromList(array $parts): array
    {
        $item = [];
        $jurusanValues = array_map(fn ($j) => $j->value, Jurusan::cases());

        foreach ($parts as $p) {
            $p = trim($p);
            if ($p === '') {
                continue;
            }

            // NIS (4-10 digit)
            if (empty($item['nis']) && preg_match('/^\d{4,10}$/', $p)) {
                $item['nis'] = $p;
                continue;
            }

            // NISN (10 digit diawali 00 atau angka panjang)
            if (empty($item['nisn']) && preg_match('/^\d{10}$/', $p) && ! empty($item['nis'])) {
                $item['nisn'] = $p;
                continue;
            }

            // Jurusan
            if (empty($item['jurusan']) && in_array(strtoupper($p), $jurusanValues, true)) {
                $item['jurusan'] = strtoupper($p);
                continue;
            }

            // Gender (L/P/Laki-laki/Perempuan)
            if (empty($item['gender']) && preg_match('/^(L|P|LAKI[_-]LAKI|PEREMPUAN)$/i', $p)) {
                $item['gender'] = $p;
                continue;
            }

            // Kelas (contoh: 10 RPL 1, X DKV 2, XI TKP)
            if (empty($item['kelas']) && preg_match('/^(10|11|12|X|XI|XII)\b/i', $p)) {
                $item['kelas'] = $p;
                continue;
            }

            // No Absen (angka 1-60)
            if (empty($item['no_absen']) && is_numeric($p) && (int) $p <= 60 && ! empty($item['nis'])) {
                $item['no_absen'] = (int) $p;
                continue;
            }

            // Jika belum ada nama dan mengandung huruf alfabet
            if (empty($item['nama']) && preg_match('/[a-zA-Z]/', $p)) {
                $item['nama'] = $p;
            }
        }

        return $item;
    }

    /**
     * Membersihkan dan memetakan nama header kolom menjadi kunci standar
     */
    protected static function cleanHeader(string $raw): string
    {
        $clean = strtolower(trim($raw));
        $clean = preg_replace('/[^a-z0-9_]/', '_', $clean);
        $clean = trim((string) preg_replace('/_+/', '_', $clean), '_');

        return match (true) {
            str_contains($clean, 'nisn') => 'nisn',
            str_contains($clean, 'nis') || str_contains($clean, 'nomor_induk') || str_contains($clean, 'no_induk') => 'nis',
            // Format DPT: kolom "username" berisi NIS.
            $clean === 'username' || $clean === 'user_name' || $clean === 'nis_username' => 'nis',
            str_contains($clean, 'nama') || $clean === 'fullname' || $clean === 'full_name' => 'nama',
            str_contains($clean, 'kelas') || str_contains($clean, 'tingkat') || str_contains($clean, 'rombel') || $clean === 'group' || $clean === 'grup' || $clean === 'kelompok' => 'kelas',
            str_contains($clean, 'jurusan') || str_contains($clean, 'kejuruan') || str_contains($clean, 'prodi') => 'jurusan',
            // Kolom tipe pengguna (DPT: STUDENT/TEACHER/STAFF) — dipakai untuk
            // melewati baris non-siswa, bukan data siswa.
            $clean === 'usertype' || $clean === 'user_type' || $clean === 'tipe_pengguna' || $clean === 'kategori' => 'usertype',
            str_contains($clean, 'gender') || str_contains($clean, 'jk') || str_contains($clean, 'jenis_kelamin') || $clean === 'l_p' || $clean === 'sex' => 'gender',
            str_contains($clean, 'absen') || str_contains($clean, 'no_absen') || $clean === 'no_urut' => 'no_absen',
            default => $clean,
        };
    }

    /**
     * True bila baris milik non-siswa (guru/staf) dan harus dilewati
     * tanpa dihitung sebagai baris rusak.
     */
    protected static function isNonSiswa(array $item): bool
    {
        if (! isset($item['usertype'])) {
            return false;
        }

        return ! in_array(strtoupper(trim((string) $item['usertype'])), ['STUDENT', 'SISWA', 'MURID', ''], true);
    }

    /**
     * Memvalidasi dan menormalisasi record siswa
     *
     * @return array{nis: string, nisn: ?string, nama: string, kelas: string, jurusan: string, gender: ?string, no_absen: ?int}|null
     */
    protected static function normalizeRecord(array $item): ?array
    {
        // NIS Excel kadang terbaca sebagai float ("10001.0") — rapikan.
        $nis = trim((string) ($item['nis'] ?? ''));
        if (preg_match('/^\d+\.0+$/', $nis)) {
            $nis = (string) (int) ((float) $nis);
        }
        $nis = preg_replace('/\s+/', '', $nis) ?? '';

        $nama = trim((string) ($item['nama'] ?? ''));
        $rawKelas = trim((string) ($item['kelas'] ?? ''));
        $rawJurusan = strtoupper(trim((string) ($item['jurusan'] ?? '')));
        $rawGender = strtoupper(trim((string) ($item['gender'] ?? '')));
        $rawGender = (string) preg_replace('/[\s\-]+/', '_', $rawGender);
        $nisn = trim((string) ($item['nisn'] ?? ''));
        if (preg_match('/^\d+\.0+$/', $nisn)) {
            $nisn = (string) (int) ((float) $nisn);
        }
        $nisn = preg_replace('/\s+/', '', $nisn) ?? '';
        $noAbsen = $item['no_absen'] ?? null;

        if ($nis === '' || $nama === '' || ! preg_match('/^\d{3,20}$/', $nis)) {
            return null;
        }

        // Normalisasi kelas (misal: "X RPL 1" -> "10 RPL 1")
        $kelas = \App\Support\Kelas::normalisasi($rawKelas) ?? $rawKelas;
        if ($kelas === '') {
            return null;
        }

        // Deteksi jurusan otomatis dari kelas jika kolom jurusan belum ada / salah.
        // Urutkan kode terpanjang dulu agar "TP" tidak kalah oleh "TKP", dsb.
        // Lookahead (?![A-Z]) agar "DKV1" tetap terdeteksi sebagai DKV.
        $jurusanValues = array_map(fn ($j) => $j->value, Jurusan::cases());
        usort($jurusanValues, fn ($a, $b) => strlen($b) <=> strlen($a));
        if (! in_array($rawJurusan, $jurusanValues, true)) {
            foreach ($jurusanValues as $jv) {
                if (preg_match('/\b'.preg_quote($jv, '/').'(?![A-Z])/i', $kelas)) {
                    $rawJurusan = $jv;
                    break;
                }
            }
        }

        if (! in_array($rawJurusan, $jurusanValues, true)) {
            return null;
        }

        // Normalisasi gender
        $gender = null;
        if ($rawGender !== '') {
            if (in_array($rawGender, ['L', 'LAKI_LAKI', 'PRIA', 'MALE'], true)) {
                $gender = Gender::LAKI_LAKI->value;
            } elseif (in_array($rawGender, ['P', 'PEREMPUAN', 'WANITA', 'FEMALE'], true)) {
                $gender = Gender::PEREMPUAN->value;
            } elseif (in_array($rawGender, array_map(fn ($g) => $g->value, Gender::cases()), true)) {
                $gender = $rawGender;
            }
        }

        return [
            'nis' => $nis,
            'nisn' => ($nisn !== '') ? $nisn : null,
            'nama' => $nama,
            'kelas' => $kelas,
            'jurusan' => $rawJurusan,
            'gender' => $gender,
            'no_absen' => is_numeric($noAbsen) ? (int) $noAbsen : null,
        ];
    }
}
