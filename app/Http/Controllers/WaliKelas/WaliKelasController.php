<?php

namespace App\Http\Controllers\WaliKelas;

use App\Enums\PeriodeStatus;
use App\Enums\Role;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\AppSetting;
use App\Models\ClassHistory;
use App\Models\Periode;
use App\Models\Registration;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WaliKelasController extends Controller
{
    private function resolveKelas(Request $request): array
    {
        $user = $request->user();

        // 1. Admin, Operator, atau Kepala Sekolah: dapat meninjau semua kelas dan berpindah kelas
        if ($user->isAdmin() || $user->hasRole(Role::OPERATOR, Role::KEPALA_SEKOLAH)) {
            $allClasses = SchoolClass::orderBy('tingkat')->orderBy('nama')->get();
            $kelas = null;

            if ($request->filled('kelas_id')) {
                $kelas = SchoolClass::find($request->query('kelas_id'));
            } elseif ($request->filled('kelas')) {
                $kelas = SchoolClass::where('nama', $request->query('kelas'))->first();
            }

            if (! $kelas) {
                $kelas = $user->managedClass ?? $allClasses->first();
            }

            return [$kelas, $allClasses];
        }

        // 2. Akun Wali Kelas: HANYA terkunci pada rombel kelas binaannya sendiri
        $kelas = $user->managedClass;
        if (! $kelas) {
            $kelas = SchoolClass::where('wali_kelas_id', $user->id)
                ->orWhere('wali_kelas_nama', $user->name)
                ->first();
        }

        // Jangan pernah mengambil kelas acak orang lain jika wali kelas belum ditugaskan
        return [$kelas, collect($kelas ? [$kelas] : [])];
    }

    private function periodeAktif(Request $request): ?Periode
    {
        $periodes = Periode::orderByDesc('periode')->get();
        $diminta = $request->query('periode');

        if ($diminta) {
            return $periodes->firstWhere('periode', $diminta) ?? $periodes->first();
        }

        return Periode::where('status', PeriodeStatus::DIBUKA)->orderByDesc('periode')->first()
            ?? $periodes->first();
    }

    public function dashboard(Request $request): View
    {
        [$kelas, $allClasses] = $this->resolveKelas($request);
        $periode = $this->periodeAktif($request);
        $periodes = Periode::orderByDesc('periode')->get();

        $students = collect();
        $registrations = collect();

        if ($kelas) {
            $students = Student::where('kelas', $kelas->nama)->orderBy('nama')->get();

            if ($periode) {
                $registrations = Registration::with(['user', 'student'])
                    ->where('kelas', $kelas->nama)
                    ->where('periode', $periode->periode)
                    ->where('status', Status::APPROVED)
                    ->get();
            }
        }

        $totalSiswa = $students->count();
        $totalIkutEskul = $registrations->pluck('nis')->unique()->count();
        $belumIkut = max(0, $totalSiswa - $totalIkutEskul);
        $sudahDinilai = $registrations->whereNotNull('nilai')->count();

        return view('wali-kelas.dashboard', compact(
            'kelas',
            'allClasses',
            'periode',
            'periodes',
            'totalSiswa',
            'totalIkutEskul',
            'belumIkut',
            'sudahDinilai',
            'registrations'
        ));
    }

    public function siswa(Request $request): View
    {
        [$kelas, $allClasses] = $this->resolveKelas($request);
        $periode = $this->periodeAktif($request);
        $periodes = Periode::orderByDesc('periode')->get();

        $students = collect();
        $regPeta = collect();
        $calonSiswa = collect();
        $tingkatKelas = null;
        $daftarKelasAsal = collect();

        if ($kelas) {
            $tingkatKelas = \App\Support\Kelas::tingkat($kelas->nama);

            // Calon siswa: siswa aktif yang saat ini belum terdaftar di kelas rombel ini
            $calonSiswa = Student::where('status', 'AKTIF')
                ->where(function ($q) use ($kelas) {
                    $q->whereNull('kelas')
                      ->orWhere('kelas', '!=', $kelas->nama);
                })
                ->orderBy('kelas')
                ->orderBy('nama')
                ->get(['id', 'nis', 'nama', 'gender', 'kelas']);

            $daftarKelasAsal = $calonSiswa->pluck('kelas')->filter()->unique()->values();

            $students = Student::where('kelas', $kelas->nama)
                ->orderBy('no_absen')
                ->orderBy('nama')
                ->get();

            if ($periode) {
                $regPeta = Registration::where('kelas', $kelas->nama)
                    ->where('periode', $periode->periode)
                    ->where('status', Status::APPROVED)
                    ->get()
                    ->keyBy('nis');
            }
        }

        return view('wali-kelas.siswa', compact(
            'kelas',
            'allClasses',
            'periode',
            'periodes',
            'students',
            'regPeta',
            'calonSiswa',
            'tingkatKelas',
            'daftarKelasAsal'
        ));
    }

    public function masukkanSiswa(Request $request): RedirectResponse
    {
        [$kelas] = $this->resolveKelas($request);

        if (! $kelas) {
            return back()->with('error', 'Anda belum ditugaskan ke rombel kelas mana pun.');
        }

        $data = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['required', 'integer', 'exists:students,id'],
        ]);

        $students = Student::whereIn('id', $data['student_ids'])->where('status', 'AKTIF')->get();
        $waliNama = $kelas->wali_kelas_nama ?? auth()->user()->name;
        $periodeStr = Periode::where('status', PeriodeStatus::DIBUKA)->first()?->periode ?? date('Y');
        $count = 0;

        DB::transaction(function () use ($students, $kelas, $waliNama, $periodeStr, &$count) {
            foreach ($students as $s) {
                $kelasLama = $s->kelas;
                $s->update([
                    'kelas' => $kelas->nama,
                    'wali_nama' => $waliNama,
                ]);

                if ($s->user_id) {
                    ClassHistory::create([
                        'user_id' => $s->user_id,
                        'periode' => $periodeStr,
                        'kelas_lama' => $kelasLama,
                        'kelas_baru' => $kelas->nama,
                        'actor_id' => (string) auth()->id(),
                        'reason' => 'Dimasukkan ke rombel oleh Wali Kelas ' . $waliNama,
                    ]);
                }
                $count++;
            }
        });

        AdminLog::record('wali_kelas.add_students', 'Student', null, "Wali kelas {$waliNama} memasukkan {$count} siswa ke rombel {$kelas->nama}.");

        return back()->with('status', "Berhasil menambahkan {$count} siswa ke dalam rombel {$kelas->nama}.");
    }

    public function keluarkanSiswa(Request $request): RedirectResponse
    {
        [$kelas] = $this->resolveKelas($request);

        if (! $kelas) {
            return back()->with('error', 'Anda belum ditugaskan ke rombel kelas mana pun.');
        }

        $data = $request->validate([
            'student_id' => ['required', 'integer', 'exists:students,id'],
        ]);

        $student = Student::where('id', $data['student_id'])->where('kelas', $kelas->nama)->firstOrFail();
        $waliNama = $kelas->wali_kelas_nama ?? auth()->user()->name;

        $student->update([
            'kelas' => null,
        ]);

        AdminLog::record('wali_kelas.remove_student', 'Student', (string) $student->id, "Wali kelas {$waliNama} mengeluarkan siswa {$student->nama} ({$student->nis}) dari rombel {$kelas->nama}.");

        return back()->with('status', "Siswa {$student->nama} telah dikeluarkan dari rombel {$kelas->nama}.");
    }

    public function bulkKeluarkanSiswa(Request $request): RedirectResponse
    {
        [$kelas] = $this->resolveKelas($request);

        if (! $kelas) {
            return back()->with('error', 'Anda belum ditugaskan ke rombel kelas mana pun.');
        }

        $data = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['required', 'integer', 'exists:students,id'],
        ]);

        $waliNama = $kelas->wali_kelas_nama ?? auth()->user()->name;
        $count = Student::whereIn('id', $data['student_ids'])
            ->where('kelas', $kelas->nama)
            ->update(['kelas' => null]);

        AdminLog::record('wali_kelas.bulk_remove_students', 'Student', null, "Wali kelas {$waliNama} mengeluarkan {$count} siswa secara massal dari rombel {$kelas->nama}.");

        return back()->with('status', "Berhasil mengeluarkan {$count} siswa dari rombel {$kelas->nama}.");
    }

    public function nilai(Request $request): View
    {
        [$kelas, $allClasses] = $this->resolveKelas($request);
        $periode = $this->periodeAktif($request);
        $periodes = Periode::orderByDesc('periode')->get();

        $dataNilai = collect();

        if ($kelas && $periode) {
            $dataNilai = Registration::with(['user', 'student'])
                ->where('kelas', $kelas->nama)
                ->where('periode', $periode->periode)
                ->where('status', Status::APPROVED)
                ->orderBy('nis')
                ->get();
        }

        return view('wali-kelas.nilai', compact(
            'kelas',
            'allClasses',
            'periode',
            'periodes',
            'dataNilai'
        ));
    }

    public function cetakNilai(Request $request): View
    {
        [$kelas, $allClasses] = $this->resolveKelas($request);
        $periode = $this->periodeAktif($request);

        $dataNilai = collect();

        if ($kelas && $periode) {
            $dataNilai = Registration::with(['user', 'student'])
                ->where('kelas', $kelas->nama)
                ->where('periode', $periode->periode)
                ->where('status', Status::APPROVED)
                ->orderBy('nis')
                ->get();
        }

        $sekolahNama = AppSetting::get('school.name', 'SMK NEGERI BAGIMU NEGERI');
        $sekolahAlamat = AppSetting::get('school.address', 'Jl. Pendidikan No. 1');
        $kepsekNama = AppSetting::get('school.kepsek', 'Kepala Sekolah');

        return view('wali-kelas.cetak-nilai', compact(
            'kelas',
            'periode',
            'dataNilai',
            'sekolahNama',
            'sekolahAlamat',
            'kepsekNama'
        ));
    }

    public function exportNilai(Request $request)
    {
        [$kelas] = $this->resolveKelas($request);
        $periode = $this->periodeAktif($request);

        if (! $kelas || ! $periode) {
            return back()->with('error', 'Data rombel atau periode tidak valid.');
        }

        $dataNilai = Registration::with(['user', 'student'])
            ->where('kelas', $kelas->nama)
            ->where('periode', $periode->periode)
            ->where('status', Status::APPROVED)
            ->orderBy('nis')
            ->get();

        $filename = 'Rekap_Nilai_' . str_replace([' ', '/', '\\'], '_', $kelas->nama) . '_' . str_replace('/', '-', $periode->periode) . '.xls';

        $output = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        $output .= '<head><meta charset="utf-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Nilai Eskul</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>';
        $output .= '<body>';
        $output .= '<h3>REKAPITULASI NILAI EKSTRAKURIKULER RAPOR</h3>';
        $output .= '<p><strong>Rombel Kelas:</strong> ' . e($kelas->nama) . ' | <strong>Tahun Ajaran:</strong> ' . e($periode->periode) . ' | <strong>Wali Kelas:</strong> ' . e($kelas->wali_kelas_nama ?? auth()->user()->name) . '</p>';
        $output .= '<table border="1" cellpadding="5" cellspacing="0">';
        $output .= '<tr style="background-color: #1e3a8a; color: #ffffff; font-weight: bold;">';
        $output .= '<th>No</th><th>NIS</th><th>NISN</th><th>Nama Lengkap Siswa</th><th>L/P</th><th>Ekstrakurikuler</th><th>Nilai Angka</th><th>Predikat</th><th>Catatan Capaian / Deskripsi Rapor</th>';
        $output .= '</tr>';

        $no = 1;
        foreach ($dataNilai as $reg) {
            $student = $reg->student;
            $output .= '<tr>';
            $output .= '<td align="center">' . $no++ . '</td>';
            $output .= '<td style="mso-number-format:\'@\';">' . e($reg->nis) . '</td>';
            $output .= '<td style="mso-number-format:\'@\';">' . e($student?->nisn ?: '-') . '</td>';
            $output .= '<td>' . e($student?->nama ?: ($reg->user?->name ?: '-')) . '</td>';
            $output .= '<td align="center">' . e($reg->gender?->value ?: ($student?->gender?->value ?: '-')) . '</td>';
            $output .= '<td>' . e($reg->eskul_utama) . '</td>';
            $output .= '<td align="center"><strong>' . ($reg->nilai !== null ? e($reg->nilai) : '-') . '</strong></td>';
            $output .= '<td align="center"><strong>' . e($reg->predikat ?: '-') . '</strong></td>';
            $output .= '<td>' . e($reg->catatan_nilai ?: '-') . '</td>';
            $output .= '</tr>';
        }

        $output .= '</table>';
        $output .= '</body></html>';

        return response($output, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }
}

