<?php

namespace App\Http\Controllers\Admin;

use App\Enums\IzinStatus;
use App\Enums\Jurusan;
use App\Enums\NotifStatus;
use App\Enums\PeriodeStatus;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\ActivityPlan;
use App\Models\Eskul;
use App\Models\Izin;
use App\Models\NotificationLog;
use App\Models\Periode;
use App\Models\Registration;

class DashboardController extends Controller
{
    public function index()
    {
        $periodeAktif = Periode::where('status', PeriodeStatus::DIBUKA)->orderByDesc('periode')->first();
        $periode = $periodeAktif?->periode;

        $cakupan = fn () => Registration::query()->when($periode, fn ($q) => $q->where('periode', $periode));
        $total = $cakupan()->count();
        $pending = $cakupan()->where('status', Status::PENDING)->count();
        $approved = $cakupan()->where('status', Status::APPROVED)->count();
        $rejected = $cakupan()->where('status', Status::REJECTED)->count();

        $eskulCounts = Registration::query()
            ->when($periode, fn ($q) => $q->where('periode', $periode))
            ->groupBy('eskul_utama')
            ->selectRaw('eskul_utama, count(*) as c')
            ->pluck('c', 'eskul_utama');

        $distEskul = Eskul::orderBy('name')->get()->map(fn ($e) => [
            'eskul' => $e,
            'count' => (int) ($eskulCounts[$e->name] ?? 0),
        ])->sortByDesc('count')->values();
        $maxEskul = max(1, $distEskul->max('count') ?? 0);

        $jurusanCounts = Registration::query()
            ->when($periode, fn ($q) => $q->where('periode', $periode))
            ->groupBy('jurusan')
            ->selectRaw('jurusan, count(*) as c')
            ->pluck('c', 'jurusan');

        $distJurusan = collect(Jurusan::cases())->map(fn ($j) => [
            'label' => $j->short(),
            'count' => (int) ($jurusanCounts[$j->value] ?? 0),
        ]);
        $maxJurusan = max(1, $distJurusan->max('count') ?? 0);

        $tugas = [];
        $izinPending = Izin::where('status', IzinStatus::PENDING)->count();
        if ($izinPending > 0) {
            $tugas[] = [
                'label' => "{$izinPending} pengajuan izin menunggu",
                'url' => route('admin.izin.index', ['status' => 'PENDING']),
            ];
        }

        foreach ($distEskul as $row) {
            $e = $row['eskul'];
            if ($e->kuota_aktif && $row['count'] >= $e->kuota) {
                $tugas[] = [
                    'label' => "Kuota {$e->name} penuh ({$row['count']}/{$e->kuota})",
                    'url' => route('admin.eskuls.show', $e),
                ];
            }
        }

        foreach (Eskul::where('aktif', true)->whereNull('pembina_id')->get() as $e) {
            $tugas[] = [
                'label' => "{$e->name} belum punya pembina",
                'url' => route('admin.eskuls.show', $e),
            ];
        }

        foreach ($distEskul->filter(fn ($r) => $r['eskul']->aktif && $r['count'] === 0) as $row) {
            $tugas[] = [
                'label' => "{$row['eskul']->name} belum punya anggota",
                'url' => route('admin.eskuls.show', $row['eskul']),
            ];
        }

        $notifGagal = NotificationLog::where('status', NotifStatus::GAGAL)->count();
        if ($notifGagal > 0) {
            $tugas[] = [
                'label' => "{$notifGagal} notifikasi gagal terkirim",
                'url' => route('admin.logs.index'),
            ];
        }

        $kegiatan = ActivityPlan::whereDate('tanggal', '>=', today())
            ->whereDate('tanggal', '<=', today()->addDays(7))
            ->count();
        if ($kegiatan > 0) {
            $tugas[] = ['label' => "{$kegiatan} kegiatan 7 hari ke depan", 'url' => null];
        }

        $terbaru = Registration::with('user')
            ->when($periode, fn ($q) => $q->where('periode', $periode))
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'periode',
            'total',
            'pending',
            'approved',
            'rejected',
            'distEskul',
            'maxEskul',
            'distJurusan',
            'maxJurusan',
            'tugas',
            'terbaru'
        ));
    }
}
