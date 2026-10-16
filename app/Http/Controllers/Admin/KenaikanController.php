<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\ClassHistory;
use App\Models\Periode;
use App\Models\PeriodeStatus;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentKeluar;
use App\Support\Kelas;
use App\Support\LoginToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KenaikanController extends Controller
{
    /**
     * Tampilan Kenaikan Kelas Berbasis Per Rombel Kelas
     */
    public function index(Request $request): View
    {
        // Ambil daftar kelas dari SchoolClass dan Student
        $fromMaster = SchoolClass::orderBy('nama')->pluck('nama');
        $fromStudents = Student::select('kelas')->distinct()->whereNotNull('kelas')->pluck('kelas');

        $availableClasses = $fromMaster->merge($fromStudents)->unique()->sort(function ($a, $b) {
            $ta = Kelas::tingkat($a) ?? 99;
            $tb = Kelas::tingkat($b) ?? 99;
            if ($ta !== $tb) {
                return $ta <=> $tb;
            }
            return strnatcasecmp($a, $b);
        })->values();

        $selectedClass = $request->query('kelas', $availableClasses->first() ?? '');
        $targetKelas = $request->query('target_kelas', $selectedClass ? Kelas::naikkan($selectedClass) : '');

        $students = collect();
        if ($selectedClass) {
            $students = Student::where('kelas', $selectedClass)
                ->where('status', 'AKTIF')
                ->orderBy('nis')
                ->get();
        }

        $totalArsipKeluar = StudentKeluar::count();

        return view('admin.kenaikan.index', compact(
            'availableClasses',
            'selectedClass',
            'targetKelas',
            'students',
            'totalArsipKeluar'
        ));
    }

    /**
     * Proses Kenaikan Kelas per Rombel Kelas dengan Checklist
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kelas_asal' => ['required', 'string'],
            'kelas_tujuan' => ['required', 'string', 'max:50'],
            'siswa_ids' => ['required', 'array'],
            'siswa_ids.*' => ['required', 'exists:students,id'],
            'naik_siswa_ids' => ['nullable', 'array'],
            'keluar_siswa_ids' => ['nullable', 'array'],
            'alasan_keluar' => ['nullable', 'array'],
        ]);

        $kelasAsal = $data['kelas_asal'];
        $kelasTujuan = Kelas::normalisasi($data['kelas_tujuan']);
        $tingkatTujuan = Kelas::tingkat($kelasTujuan);

        $naikIds = array_flip($data['naik_siswa_ids'] ?? []);
        $keluarIds = array_flip($data['keluar_siswa_ids'] ?? []);

        $periodeAktif = Periode::where('status', PeriodeStatus::DIBUKA)->orderByDesc('periode')->first();
        $periodeStr = $periodeAktif?->periode ?? date('Y');

        $naikCount = 0;
        $tinggalCount = 0;
        $keluarCount = 0;

        DB::transaction(function () use (
            $data,
            $kelasAsal,
            $kelasTujuan,
            $tingkatTujuan,
            $naikIds,
            $keluarIds,
            $periodeStr,
            &$naikCount,
            &$tinggalCount,
            &$keluarCount
        ) {
            $students = Student::whereIn('id', $data['siswa_ids'])->get();

            foreach ($students as $s) {
                $idStr = (string) $s->id;

                // 1. Dikeluarkan / Mutasi -> Simpan ke students_keluar
                if (isset($keluarIds[$idStr])) {
                    $alasan = $data['alasan_keluar'][$s->id] ?? 'Dikeluarkan / mutasi saat kenaikan kelas';

                    StudentKeluar::create([
                        'student_id' => $s->id,
                        'nis' => $s->nis,
                        'nisn' => $s->nisn,
                        'nama' => $s->nama,
                        'kelas_terakhir' => $s->kelas,
                        'jurusan' => $s->jurusan?->value ?? (preg_match('/(RPL|DKV|KULINER|TP|TKP)/i', $s->kelas, $m) ? strtoupper($m[1]) : null),
                        'whatsapp' => $s->whatsapp,
                        'status_keluar' => 'KELUAR',
                        'alasan' => $alasan,
                        'tanggal_keluar' => today(),
                        'diproses_oleh' => auth()->user()->name,
                    ]);

                    $s->update(['status' => 'KELUAR']);

                    if ($s->user) {
                        $s->user->update(['student_status' => 'NONAKTIF']);
                        LoginToken::revoke($s->user);
                        $s->user->increment('session_version');
                    }

                    $keluarCount++;
                    continue;
                }

                // 2. Naik Kelas (Dicentang)
                if (isset($naikIds[$idStr])) {
                    $statusBaru = 'AKTIF';
                    if ($tingkatTujuan !== null && $tingkatTujuan >= 12) {
                        $statusBaru = 'NONAKTIF'; // Kelas 12 tidak ikut eskul
                    }

                    $s->update([
                        'kelas' => $kelasTujuan,
                        'status' => $statusBaru,
                    ]);

                    if ($s->user) {
                        $s->user->update(['student_status' => $statusBaru]);
                    }

                    if ($s->user_id) {
                        ClassHistory::create([
                            'user_id' => $s->user_id,
                            'periode' => $periodeStr,
                            'kelas_lama' => $kelasAsal,
                            'kelas_baru' => $kelasTujuan,
                            'actor_id' => (string) auth()->id(),
                            'reason' => 'Kenaikan kelas per rombel',
                        ]);
                    }

                    $naikCount++;
                } else {
                    // 3. Tinggal Kelas (Centang Dicopot)
                    // Siswa tetap di kelas asal
                    if ($s->user_id) {
                        ClassHistory::create([
                            'user_id' => $s->user_id,
                            'periode' => $periodeStr,
                            'kelas_lama' => $kelasAsal,
                            'kelas_baru' => $kelasAsal,
                            'actor_id' => (string) auth()->id(),
                            'reason' => 'Tinggal kelas (tidak dicentang naik)',
                        ]);
                    }

                    $tinggalCount++;
                }
            }
        });

        AdminLog::record('kenaikan.proses', 'Student', $kelasAsal, "Kenaikan kelas rombel {$kelasAsal} -> {$kelasTujuan}: {$naikCount} naik, {$tinggalCount} tinggal kelas, {$keluarCount} diarsipkan keluar.");

        return redirect()->route('admin.kenaikan.index', ['kelas' => $kelasAsal])
            ->with('status', "Proses rombel {$kelasAsal} selesai: {$naikCount} siswa naik ke {$kelasTujuan}, {$tinggalCount} siswa tinggal kelas, {$keluarCount} siswa diarsipkan ke tabel siswa keluar.");
    }

    /**
     * Keluarkan siswa individual langsung dari tombol aksi
     */
    public function keluarkanSiswa(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'status_keluar' => ['required', 'in:KELUAR,MUTASI,DO'],
            'alasan' => ['required', 'string', 'max:500'],
        ]);

        $student = Student::findOrFail($data['student_id']);

        StudentKeluar::create([
            'student_id' => $student->id,
            'nis' => $student->nis,
            'nisn' => $student->nisn,
            'nama' => $student->nama,
            'kelas_terakhir' => $student->kelas,
            'jurusan' => $student->jurusan?->value ?? (preg_match('/(RPL|DKV|KULINER|TP|TKP)/i', $student->kelas, $m) ? strtoupper($m[1]) : null),
            'whatsapp' => $student->whatsapp,
            'status_keluar' => $data['status_keluar'],
            'alasan' => $data['alasan'],
            'tanggal_keluar' => today(),
            'diproses_oleh' => auth()->user()->name,
        ]);

        $student->update(['status' => $data['status_keluar']]);

        if ($student->user) {
            $student->user->update(['student_status' => 'NONAKTIF']);
            LoginToken::revoke($student->user);
            $student->user->increment('session_version');
        }

        AdminLog::record('siswa.keluar', 'Student', (string) $student->id, "Siswa {$student->nama} ({$student->nis}) berstatus {$data['status_keluar']}. Data diarsipkan.");

        return back()->with('status', "Siswa {$student->nama} berhasil diarsipkan ke tabel Siswa Keluar dengan status {$data['status_keluar']}.");
    }

    /**
     * Halaman Arsip Siswa Keluar / Mutasi
     */
    public function arsipKeluar(): View
    {
        $arsip = StudentKeluar::orderByDesc('tanggal_keluar')
            ->orderByDesc('id')
            ->paginate(30);

        return view('admin.kenaikan.arsip-keluar', compact('arsip'));
    }
}
