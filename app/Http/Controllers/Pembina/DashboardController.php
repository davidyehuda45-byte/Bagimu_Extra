<?php

namespace App\Http\Controllers\Pembina;

use App\Enums\IzinStatus;
use App\Enums\PeriodeStatus;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\ActivityPlan;
use App\Models\Eskul;
use App\Models\Izin;
use App\Models\Periode;
use App\Models\Registration;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $me = $request->user();

        // Aturan bisnis: 1 akun pembina = 1 eskul
        $milik = Eskul::where('pembina_id', $me->id)
            ->orWhere(fn ($q) => $q->where('temp_pembina_id', $me->id)->whereDate('temp_pembina_sampai', '>=', today()));

        $eskul = (clone $milik)->withCount('jadwals')->first();
        $eskuls = $eskul ? collect([$eskul]) : collect();
        $milikIds = $eskul ? [$eskul->id] : [];
        $milikNames = $eskul ? [$eskul->name] : [];

        $periodeAktif = Periode::where('status', PeriodeStatus::DIBUKA)->pluck('periode');

        $pendaftarAktif = Registration::query()
            ->whereIn('periode', $periodeAktif)
            ->whereIn('eskul_utama', $milikNames)
            ->where('status', Status::APPROVED)
            ->count();

        $pendaftarPending = Registration::query()
            ->whereIn('periode', $periodeAktif)
            ->whereIn('eskul_utama', $milikNames)
            ->where('status', Status::PENDING)
            ->count();

        $izinPending = Izin::query()
            ->where('status', IzinStatus::PENDING)
            ->whereIn('eskul_id', $milikIds)
            ->count();

        $kegiatanMendatang = ActivityPlan::with('eskul')
            ->whereIn('eskul_id', $milikIds)
            ->whereDate('tanggal', '>=', today())
            ->orderBy('tanggal')
            ->take(5)
            ->get();

        $stat = [];
        if ($eskul) {
            $cari = Registration::whereIn('periode', $periodeAktif)
                ->where('eskul_utama', $eskul->name);
            $stat[$eskul->id] = [
                'pending' => (clone $cari)->where('status', Status::PENDING)->count(),
                'approved' => (clone $cari)->where('status', Status::APPROVED)->count(),
            ];
        }

        return view('pembina.dashboard', compact(
            'eskul',
            'eskuls',
            'pendaftarAktif',
            'pendaftarPending',
            'izinPending',
            'kegiatanMendatang',
            'stat'
        ));
    }
}
