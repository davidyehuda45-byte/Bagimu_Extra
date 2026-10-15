<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Student;
use App\Support\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KenaikanController extends Controller
{
    /**
     * Pratinjau pemetaan: 10→11 (tetap aktif), 11→12 (nonaktif),
     * 12→LULUS (nonaktif). Di luar itu dilewati.
     */
    public function index()
    {
        $peta = Student::orderBy('kelas')->orderBy('nis')->get()->map(function ($s) {
            if ($s->status !== 'AKTIF') {
                return [
                    'student' => $s,
                    'tingkat' => Kelas::tingkat($s->kelas),
                    'kelasBaru' => $s->kelas,
                    'aksi' => "Dilewati (status {$s->status})",
                    'statusBaru' => $s->status,
                ];
            }

            $tingkat = Kelas::tingkat($s->kelas);
            $baru = $tingkat !== null && $tingkat < 12 ? Kelas::naikkan($s->kelas) : null;

            if ($tingkat === null) {
                $aksi = 'Dilewati (format kelas tak dikenal)';
                $statusBaru = $s->status;
            } elseif ($tingkat < 11) {
                $aksi = "Naik ke {$baru} (tetap aktif)";
                $statusBaru = 'AKTIF';
            } elseif ($tingkat === 11) {
                $aksi = "Naik ke {$baru} (otomatis nonaktif — kelas 12 tidak ikut eskul)";
                $statusBaru = 'NONAKTIF';
            } else {
                $aksi = 'Lulus (otomatis nonaktif)';
                $statusBaru = 'LULUS';
            }

            return [
                'student' => $s,
                'tingkat' => $tingkat,
                'kelasBaru' => $baru,
                'aksi' => $aksi,
                'statusBaru' => $statusBaru,
            ];
        });

        return view('admin.kenaikan.index', compact('peta'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'konfirmasi' => ['required', 'accepted'],
        ]);

        $diproses = 0;
        $dilewati = 0;

        $periodeAktif = \App\Models\Periode::where('status', \App\Enums\PeriodeStatus::DIBUKA)->orderByDesc('periode')->first();
        $periodeStr = $periodeAktif?->periode ?? date('Y');

        DB::transaction(function () use (&$diproses, &$dilewati, $periodeStr) {
            foreach (Student::orderBy('id')->get() as $s) {
                if ($s->status !== 'AKTIF') {
                    $dilewati++;
                    continue;
                }

                $tingkat = Kelas::tingkat($s->kelas);

                if ($tingkat === null) {
                    $dilewati++;
                    continue;
                }

                $kelasLama = $s->kelas;

                if ($tingkat < 11) {
                    $baru = Kelas::naikkan($s->kelas);
                    $s->update(['kelas' => $baru, 'status' => 'AKTIF']);
                    $s->user?->update(['student_status' => 'AKTIF']);
                } elseif ($tingkat === 11) {
                    $baru = Kelas::naikkan($s->kelas);
                    $s->update(['kelas' => $baru, 'status' => 'NONAKTIF']);
                    $s->user?->update(['student_status' => 'NONAKTIF']);
                } else {
                    $baru = 'LULUS';
                    $s->update(['status' => 'LULUS']);
                    $s->user?->update(['student_status' => 'LULUS']);
                    // Paksa keluar: cabut token login + naikkan versi sesi.
                    if ($s->user) {
                        \App\Support\LoginToken::revoke($s->user);
                        $s->user->increment('session_version');
                    }
                }

                if ($s->user_id) {
                    \App\Models\ClassHistory::create([
                        'user_id' => $s->user_id,
                        'periode' => $periodeStr,
                        'kelas_lama' => $kelasLama,
                        'kelas_baru' => $baru,
                        'actor_id' => (string) auth()->id(),
                        'reason' => 'Kenaikan kelas massal',
                    ]);
                }

                $diproses++;
            }
        });

        AdminLog::record('kenaikan.proses', 'Student', null, "Kenaikan kelas: {$diproses} diproses, {$dilewati} dilewati.");

        return redirect()->route('admin.kenaikan.index')
            ->with('status', "Kenaikan kelas selesai: {$diproses} diproses, {$dilewati} dilewati (format kelas tak dikenal).");
    }
}
