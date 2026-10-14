<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Jurusan;
use App\Enums\KehadiranStatus;
use App\Enums\PeriodeStatus;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Eskul;
use App\Models\Kehadiran;
use App\Models\Periode;
use App\Models\Registration;
use App\Models\StatusHistory;
use App\Models\User;
use App\Support\CsvExport;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $periodes = Periode::orderByDesc('periode')->get();
        $aktif = Periode::where('status', PeriodeStatus::DIBUKA)->orderByDesc('periode')->first();
        $periode = $request->query('periode', $aktif?->periode ?? $periodes->first()?->periode);

        $base = Registration::query()->when($periode, fn ($q) => $q->where('periode', $periode));

        if ($request->filled('jurusan')) {
            $base->where('jurusan', $request->query('jurusan'));
        }
        if ($request->filled('kelas')) {
            $base->where('kelas', 'like', '%'.$request->query('kelas').'%');
        }
        if ($request->filled('eskul')) {
            $base->where('eskul_utama', $request->query('eskul'));
        }
        if ($request->filled('status')) {
            $base->where('status', $request->query('status'));
        }

        $total = (clone $base)->count();
        $diterima = (clone $base)->where('status', Status::APPROVED)->count();
        $menunggu = (clone $base)->where('status', Status::PENDING)->count();

        $tanpaEskul = $periode
            ? User::where('role', \App\Enums\Role::STUDENT)
                ->whereNotExists(function ($q) use ($periode) {
                    $q->selectRaw('1')->from('registrations')
                        ->whereColumn('registrations.user_id', 'users.id')
                        ->where('registrations.periode', $periode);
                })->count()
            : 0;

        $perubahan = StatusHistory::whereHas('registration', fn ($q) => $q->when($periode, fn ($qq) => $qq->where('periode', $periode)))
            ->selectRaw('new_status, COUNT(*) as c')
            ->groupBy('new_status')
            ->pluck('c', 'new_status')
            ->all();

        $kuota = Eskul::orderBy('name')->get()->map(function ($e) use ($periode) {
            $count = Registration::where('eskul_utama', $e->name)
                ->when($periode, fn ($q) => $q->where('periode', $periode))->count();

            return ['eskul' => $e, 'count' => $count];
        });

        $perJurusan = (clone $base)->selectRaw('jurusan, COUNT(*) as c')->groupBy('jurusan')->pluck('c', 'jurusan')->all();

        $nilai = (clone $base)->whereNotNull('nilai')
            ->selectRaw('predikat, COUNT(*) as c')->groupBy('predikat')->pluck('c', 'predikat')->all();

        $kehadiran = Kehadiran::whereHas('registration', fn ($q) => $q->when($periode, fn ($qq) => $qq->where('periode', $periode)))
            ->selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status')->all();

        $eskuls = Eskul::orderBy('name')->get();

        return view('admin.laporan.index', [
            'periodes' => $periodes,
            'periode' => $periode,
            'eskuls' => $eskuls,
            'total' => $total,
            'diterima' => $diterima,
            'menunggu' => $menunggu,
            'tanpaEskul' => $tanpaEskul,
            'perubahan' => $perubahan,
            'kuota' => $kuota,
            'perJurusan' => $perJurusan,
            'nilai' => $nilai,
            'kehadiran' => $kehadiran,
        ]);
    }

    public function export(Request $request)
    {
        $aktif = Periode::where('status', PeriodeStatus::DIBUKA)->orderByDesc('periode')->first();
        $periode = $request->query('periode', $aktif?->periode);

        $rekap = $this->rekap(Eskul::orderBy('name')->get(), $periode);

        $rows = $rekap->map(fn ($row, $i) => [
            $i + 1,
            $row['eskul']->name,
            $periode ?? 'Semua',
            $row['approved'],
            $row['pending'],
            $row['rejected'],
            $row['hadir'].' / '.$row['total_kehadiran'],
            $row['persen_hadir'].'%',
        ]);

        return CsvExport::download(
            'laporan-'.($periode ? str_replace('/', '-', $periode) : 'semua').'.csv',
            ['No', 'Eskul', 'Periode', 'Disetujui', 'Menunggu', 'Ditolak', 'Hadir/Total', '% Hadir'],
            $rows
        );
    }

    private function rekap($eskuls, ?string $periode)
    {
        return $eskuls->map(function (Eskul $eskul) use ($periode) {
            $base = Registration::where('eskul_utama', $eskul->name);
            if ($periode) {
                $base = (clone $base)->where('periode', $periode);
            }

            $approved = (clone $base)->where('status', Status::APPROVED)->count();
            $pending = (clone $base)->where('status', Status::PENDING)->count();
            $rejected = (clone $base)->where('status', Status::REJECTED)->count();

            $totalHadir = Kehadiran::where('eskul_id', $eskul->id)->count();
            $hadir = Kehadiran::where('eskul_id', $eskul->id)->where('status', KehadiranStatus::HADIR)->count();
            $persen = $totalHadir > 0 ? round($hadir / $totalHadir * 100, 1) : 0;

            return [
                'eskul' => $eskul,
                'approved' => $approved,
                'pending' => $pending,
                'rejected' => $rejected,
                'total_kehadiran' => $totalHadir,
                'hadir' => $hadir,
                'persen_hadir' => $persen,
            ];
        });
    }
}
