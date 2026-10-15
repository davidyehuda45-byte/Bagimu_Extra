<?php

namespace App\Http\Controllers\Siswa;

use App\Enums\PeriodeStatus;
use App\Http\Controllers\Controller;
use App\Models\Izin;
use App\Models\Kehadiran;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KehadiranController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $regIds = $user->registrations()->pluck('id');

        $eskulDiikuti = $user->registrations()
            ->get(['eskul_utama', 'eskul_cadangan'])
            ->flatMap(fn ($r) => [$r->eskul_utama, $r->eskul_cadangan])
            ->filter()
            ->unique()
            ->values();

        $filter = $request->query('eskul');
        if ($filter && ! $eskulDiikuti->contains($filter)) {
            $filter = null;
        }

        $base = Kehadiran::whereIn('registration_id', $regIds)
            ->when($filter, fn ($q) => $q->whereHas('eskul', fn ($qq) => $qq->where('name', $filter)));

        $statistik = [
            'hadir' => (clone $base)->where('status', 'HADIR')->count(),
            'izin' => (clone $base)->where('status', 'IZIN')->count(),
            'sakit' => (clone $base)->where('status', 'SAKIT')->count(),
            'alpa' => (clone $base)->where('status', 'ALPA')->count(),
        ];
        $statistik['total'] = array_sum($statistik);

        $kehadiran = (clone $base)
            ->with(['eskul', 'registration'])
            ->orderBy('tanggal', 'desc')
            ->paginate(15)
            ->withQueryString();

        $periodeAktif = Periode::where('status', PeriodeStatus::DIBUKA)->orderBy('periode', 'desc')->first();

        $pendaftaran = $periodeAktif
            ? $user->registrations()->where('periode', $periodeAktif->periode)->first()
            : null;

        $pengajuan = Izin::whereIn('registration_id', $regIds)
            ->with('eskul')
            ->latest()
            ->take(20)
            ->get();

        $persen = $statistik['total'] > 0
            ? round($statistik['hadir'] / $statistik['total'] * 100, 1)
            : 0;

        return view('siswa.kehadiran', compact(
            'kehadiran', 'statistik', 'eskulDiikuti', 'filter',
            'periodeAktif', 'pendaftaran', 'pengajuan', 'persen'
        ));
    }
}
