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

        $milik = Eskul::where('pembina_id', $me->id)->orWhere(fn ($q) => $q->where('temp_pembina_id', $me->id)->whereDate('temp_pembina_sampai', '>=', today()));

        $eskuls = (clone $milik)->withCount('jadwals')->orderBy('name')->get();
        $milikIds = (clone $milik)->pluck('id');
        $milikNames = (clone $milik)->pluck('name');

        $periodeAktif = Periode::where('status', PeriodeStatus::DIBUKA)->pluck('periode');

        $pendaftarPending = Registration::query()
            ->whereIn('periode', $periodeAktif)
            ->whereIn('eskul_utama', $milikNames)
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
        foreach ((clone $milik)->orderBy('name')->get() as $e) {
            $cari = Registration::whereIn('periode', $periodeAktif)
                ->where('eskul_utama', $e->name);
            $stat[$e->id] = [
                'pending' => (clone $cari)->where('status', Status::PENDING)->count(),
                'approved' => (clone $cari)->where('status', Status::APPROVED)->count(),
            ];
        }

        return view('pembina.dashboard', compact(
            'eskuls',
            'pendaftarPending',
            'izinPending',
            'kegiatanMendatang',
            'stat'
        ));
    }
}
