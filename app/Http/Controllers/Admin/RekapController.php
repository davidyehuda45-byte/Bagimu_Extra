<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PeriodeStatus;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Eskul;
use App\Models\Periode;
use App\Models\Registration;
use App\Support\ExcelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RekapController extends Controller
{
    private function periodeBerjalan(): ?string
    {
        return Periode::where('status', PeriodeStatus::DIBUKA)->orderByDesc('periode')->value('periode');
    }

    public function index(Request $request)
    {
        $allPeriodes = Periode::orderByDesc('periode')->get();
        $activePeriode = $allPeriodes->firstWhere('status', PeriodeStatus::DIBUKA);

        $selectedPeriode = $request->query('periode') ?: ($activePeriode?->periode ?: $allPeriodes->first()?->periode);
        $periodeModel = $allPeriodes->firstWhere('periode', $selectedPeriode);

        // Ambil seluruh data pendaftaran untuk periode terpilih
        $registrations = Registration::with('user')
            ->when($selectedPeriode, fn ($q) => $q->where('periode', $selectedPeriode))
            ->get();

        $regByEskul = $registrations->groupBy('eskul_utama');

        $rekap = Eskul::with(['pembina', 'tempPembina'])->orderBy('name')->get()->map(function ($e) use ($regByEskul) {
            $regs = $regByEskul->get($e->name, collect());

            // Sebaran asal rombel kelas siswa di eskul ini
            $kelasDist = $regs->groupBy('kelas')->map(function ($list, $k) {
                return [
                    'kelas' => $k ?: 'Tanpa Kelas',
                    'count' => $list->count(),
                ];
            })->sortBy('kelas', SORT_NATURAL)->values();

            // Pembina saat ini / pengampu
            $pembinaNama = $e->pembina?->name ?? 'Belum Ditugaskan';
            if ($e->temp_pembina_id && $e->temp_pembina_sampai && $e->temp_pembina_sampai->gte(today())) {
                $pembinaNama .= ' (Plt: ' . ($e->tempPembina?->name ?? 'Plt') . ')';
            }

            return [
                'eskul' => $e,
                'pembina_nama' => $pembinaNama,
                'pembina_kontak' => $e->pembina?->whatsapp ?? '',
                'count' => $regs->count(),
                'approved' => $regs->where('status', Status::APPROVED)->count(),
                'pending' => $regs->where('status', Status::PENDING)->count(),
                'rejected' => $regs->where('status', Status::REJECTED)->count(),
                'kelas_dist' => $kelasDist,
            ];
        })->sortByDesc('count')->values();

        $totalPendaftar = $registrations->count();
        $totalApproved = $registrations->where('status', Status::APPROVED)->count();
        $totalEskulAktif = Eskul::where('aktif', true)->count();

        return view('admin.rekap.index', compact(
            'rekap',
            'allPeriodes',
            'selectedPeriode',
            'periodeModel',
            'activePeriode',
            'totalPendaftar',
            'totalApproved',
            'totalEskulAktif'
        ));
    }

    public function show(Request $request, string $eskul)
    {
        $model = Eskul::with(['pembina', 'tempPembina'])->where('name', urldecode($eskul))->firstOrFail();

        $allPeriodes = Periode::orderByDesc('periode')->get();
        $activePeriode = $allPeriodes->firstWhere('status', PeriodeStatus::DIBUKA);
        $periode = $request->query('periode') ?: ($activePeriode?->periode ?: $allPeriodes->first()?->periode);
        $periodeModel = $allPeriodes->firstWhere('periode', $periode);

        $query = Registration::with('user')
            ->join('users', 'users.id', '=', 'registrations.user_id')
            ->select('registrations.*')
            ->where('eskul_utama', $model->name)
            ->when($periode, fn ($q) => $q->where('periode', $periode));

        if ($request->filled('kelas')) {
            $query->where('registrations.kelas', $request->input('kelas'));
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $query->where(function ($w) use ($q) {
                $w->where('users.name', 'like', "%{$q}%")
                  ->orWhere('registrations.nis', 'like', "%{$q}%");
            });
        }

        $siswa = $query->orderBy('registrations.kelas')->orderBy('users.name')->paginate(20)->withQueryString();

        // Rincian rombel kelas untuk eskul ini
        $kelasStats = Registration::where('eskul_utama', $model->name)
            ->when($periode, fn ($q) => $q->where('periode', $periode))
            ->select('kelas', DB::raw('count(*) as total'))
            ->groupBy('kelas')
            ->orderBy('kelas')
            ->get();

        $kelasList = Registration::where('eskul_utama', $model->name)
            ->when($periode, fn ($q) => $q->where('periode', $periode))
            ->whereNotNull('kelas')
            ->distinct()
            ->pluck('kelas')
            ->sort(SORT_NATURAL)
            ->values();

        $pembinaNama = $model->pembina?->name ?? 'Belum Ditugaskan';

        return view('admin.rekap.show', compact(
            'model',
            'siswa',
            'periode',
            'periodeModel',
            'allPeriodes',
            'activePeriode',
            'kelasStats',
            'kelasList',
            'pembinaNama'
        ));
    }

    public function export(Request $request)
    {
        $allPeriodes = Periode::orderByDesc('periode')->get();
        $activePeriode = $allPeriodes->firstWhere('status', PeriodeStatus::DIBUKA);
        $periode = $request->query('periode') ?: ($activePeriode?->periode ?: $allPeriodes->first()?->periode);

        $registrations = Registration::where('periode', $periode)->get();
        $regByEskul = $registrations->groupBy('eskul_utama');

        $headers = [
            'No',
            'Nama Ekstrakurikuler',
            'Pembina Eskul',
            'No. WhatsApp Pembina',
            'Kuota',
            'Total Pendaftar',
            'Disetujui',
            'Sebaran Rombel Kelas (Jumlah Siswa)',
            'Status Kuota',
        ];

        $i = 1;
        $rows = Eskul::with(['pembina', 'tempPembina'])->orderBy('name')->get()->map(function ($e) use (&$i, $regByEskul) {
            $regs = $regByEskul->get($e->name, collect());
            $count = $regs->count();
            $approved = $regs->where('status', Status::APPROVED)->count();

            $kelasRincian = $regs->groupBy('kelas')->map(function ($list, $k) {
                return ($k ?: 'Tanpa Kelas') . ': ' . $list->count();
            })->implode(', ');

            $statusKuota = $e->kuota_aktif ? ($count >= $e->kuota ? 'Penuh' : 'Tersedia (' . ($e->kuota - $count) . ' sisa)') : 'Tanpa Batas';

            return [
                $i++,
                $e->name,
                $e->pembina?->name ?? 'Belum Ditugaskan',
                $e->pembina?->whatsapp ?? '-',
                $e->kuota_aktif ? $e->kuota : 'Bebas',
                $count,
                $approved,
                $kelasRincian ?: '-',
                $statusKuota,
            ];
        });

        $namaBerkas = 'rekap-semua-eskul-periode-' . Str::slug($periode ?: 'semua') . '-' . date('Y-m-d');

        return ExcelExport::xls($namaBerkas . '.xls', $headers, $rows);
    }
}
