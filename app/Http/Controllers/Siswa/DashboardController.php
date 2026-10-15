<?php

namespace App\Http\Controllers\Siswa;

use App\Enums\IzinStatus;
use App\Enums\PeriodeStatus;
use App\Http\Controllers\Controller;
use App\Models\Eskul;
use App\Models\Izin;
use App\Models\Kehadiran;
use App\Models\Periode;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user()->load('student');

        $periodeAktif = Periode::where('status', PeriodeStatus::DIBUKA)
            ->with('window')
            ->orderBy('periode', 'desc')
            ->first();

        $pendaftaran = null;
        if ($periodeAktif) {
            $pendaftaran = $user->registrations()
                ->where('periode', $periodeAktif->periode)
                ->first();
        }

        $pengumuman = $user->inappNotifications()->latest()->take(5)->get();

        $namaEskul = $user->registrations()
            ->get(['eskul_utama'])
            ->pluck('eskul_utama')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $jadwalEskul = $namaEskul === []
            ? collect()
            : Eskul::whereIn('name', $namaEskul)->with('jadwals')->orderBy('name')->get();

        $regIds = $user->registrations()->pluck('id');

        $ringkasan = $regIds->isEmpty()
            ? collect()
            : Kehadiran::whereIn('registration_id', $regIds)
                ->with('eskul')
                ->get()
                ->groupBy(fn ($h) => $h->eskul?->name ?? 'Lainnya')
                ->map(fn ($items) => [
                    'total' => $items->count(),
                    'hadir' => $items->where(fn ($h) => $h->status?->value === 'HADIR')->count(),
                ]);

        $hadirSemua = $ringkasan->sum('hadir');
        $totalSemua = $ringkasan->sum('total');
        $persenHadir = $totalSemua > 0 ? round($hadirSemua / $totalSemua * 100, 1) : 0;

        $izinPending = $regIds->isEmpty()
            ? 0
            : Izin::whereIn('registration_id', $regIds)->where('status', IzinStatus::PENDING)->count();

        return view('siswa.dashboard', compact(
            'user',
            'periodeAktif',
            'pendaftaran',
            'pengumuman',
            'jadwalEskul',
            'ringkasan',
            'persenHadir',
            'izinPending'
        ));
    }

    public function pengumuman(): View
    {
        $user = auth()->user();
        $daftar = $user->inappNotifications()->latest()->paginate(20);

        return view('siswa.pengumuman', compact('daftar'));
    }
}
