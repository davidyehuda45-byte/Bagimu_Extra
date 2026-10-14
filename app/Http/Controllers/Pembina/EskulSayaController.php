<?php

namespace App\Http\Controllers\Pembina;

use App\Enums\IzinStatus;
use App\Enums\KehadiranStatus;
use App\Enums\PeriodeStatus;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Eskul;
use App\Models\Izin;
use App\Models\Kehadiran;
use App\Models\Periode;
use App\Models\Registration;
use App\Support\CsvExport;
use App\Support\ExcelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EskulSayaController extends Controller
{
    public function index(Request $request)
    {
        $me = $request->user();

        $milik = Eskul::where('pembina_id', $me->id)->orWhere(fn ($q) => $q->where('temp_pembina_id', $me->id)->whereDate('temp_pembina_sampai', '>=', today()));

        $eskuls = $milik->withCount('jadwals')->orderBy('name')->get();

        return view('pembina.eskul.index', compact('eskuls'));
    }

    public function show(Request $request, Eskul $eskul)
    {
        $me = $request->user();

        $isMilik = $eskul->pembina_id === $me->id
            || ($eskul->temp_pembina_id === $me->id
                && $eskul->temp_pembina_sampai
                && $eskul->temp_pembina_sampai->gte(today()));

        if (! $isMilik) {
            abort(403);
        }

        $periodeAktif = Periode::where('status', PeriodeStatus::DIBUKA)->pluck('periode');

        $pendaftar = Registration::with('user')
            ->join('users', 'users.id', '=', 'registrations.user_id')
            ->select('registrations.*')
            ->whereIn('periode', $periodeAktif)
            ->where('eskul_utama', $eskul->name)
            ->orderBy('registrations.kelas')->orderBy('users.name')
            ->get();

        $jadwals = $eskul->jadwals()->orderBy('hari')->orderBy('jam_mulai')->get();

        $izinPending = Izin::with('registration.user')
            ->where('eskul_id', $eskul->id)
            ->where('status', IzinStatus::PENDING)
            ->orderBy('tanggal')
            ->get();

        $tanggal = $request->query('tanggal', today()->toDateString());

        $disetujui = Registration::with('user')
            ->join('users', 'users.id', '=', 'registrations.user_id')
            ->select('registrations.*')
            ->whereIn('periode', $periodeAktif)
            ->where('eskul_utama', $eskul->name)
            ->where('status', Status::APPROVED)
            ->orderBy('registrations.kelas')->orderBy('users.name')
            ->get();

        $peta = Kehadiran::where('eskul_id', $eskul->id)
            ->whereDate('tanggal', $tanggal)
            ->get()->keyBy('registration_id');

        $locked = $peta->isNotEmpty() && $peta->every(fn ($k) => $k->isLocked());

        return view('pembina.eskul.show', compact('eskul', 'pendaftar', 'jadwals', 'izinPending', 'tanggal', 'disetujui', 'peta', 'locked'));
    }

    public function export(Request $request, Eskul $eskul)
    {
        $me = $request->user();

        $isMilik = $eskul->pembina_id === $me->id
            || ($eskul->temp_pembina_id === $me->id
                && $eskul->temp_pembina_sampai
                && $eskul->temp_pembina_sampai->gte(today()));

        if (! $isMilik) {
            abort(403);
        }

        $periodeAktif = Periode::where('status', PeriodeStatus::DIBUKA)->pluck('periode');

        $disetujui = Registration::with(['user', 'kehadiran' => fn ($q) => $q->where('eskul_id', $eskul->id)])
            ->join('users', 'users.id', '=', 'registrations.user_id')
            ->select('registrations.*')
            ->whereIn('periode', $periodeAktif)
            ->where('eskul_utama', $eskul->name)
            ->where('status', Status::APPROVED)
            ->orderBy('registrations.kelas')->orderBy('users.name')
            ->get();

        $headers = [
            'No',
            'NIS',
            'Nama Siswa',
            'Kelas',
            'Jurusan',
            'Jenis Kelamin',
            'Hadir',
            'Izin',
            'Sakit',
            'Alpa',
            'Total Kehadiran',
            'Nilai Akhir',
            'Predikat',
            'Catatan Nilai',
        ];

        $rows = $disetujui->map(function ($r, $index) {
            $hadirCount = $r->kehadiran->where('status', KehadiranStatus::HADIR)->count();
            $izinCount = $r->kehadiran->where('status', KehadiranStatus::IZIN)->count();
            $sakitCount = $r->kehadiran->where('status', KehadiranStatus::SAKIT)->count();
            $alpaCount = $r->kehadiran->where('status', KehadiranStatus::ALPA)->count();
            $totalPertemuan = $r->kehadiran->count();

            return [
                $index + 1,
                $r->nis ?? $r->user?->nis ?? '-',
                $r->user?->name ?? '-',
                $r->kelas ?? '-',
                $r->jurusan instanceof \BackedEnum ? $r->jurusan->value : ($r->jurusan ?? '-'),
                $r->gender instanceof \BackedEnum ? $r->gender->value : ($r->gender ?? '-'),
                $hadirCount,
                $izinCount,
                $sakitCount,
                $alpaCount,
                $totalPertemuan,
                $r->nilai ?? '-',
                $r->predikat ?? '-',
                $r->catatan_nilai ?? '-',
            ];
        });

        $slugEskul = Str::slug($eskul->name);
        $namaBerkas = 'rekap-' . $slugEskul . '-' . date('Y-m-d');

        if ($request->query('format') === 'excel') {
            return ExcelExport::xls($namaBerkas . '.xls', $headers, $rows);
        }

        return CsvExport::download($namaBerkas . '.csv', $headers, $rows);
    }
}
