<?php

namespace App\Http\Controllers\Pembina;

use App\Enums\ActivityJenis;
use App\Enums\IzinStatus;
use App\Enums\KehadiranStatus;
use App\Enums\PeriodeStatus;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\ActivityPlan;
use App\Models\AdminLog;
use App\Models\Eskul;
use App\Models\Izin;
use App\Models\Kehadiran;
use App\Models\Periode;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KegiatanController extends Controller
{
    private function milikIds(int $pembinaId)
    {
        $milik = Eskul::where('pembina_id', $pembinaId)->orWhere(fn ($q) => $q->where('temp_pembina_id', $pembinaId)->whereDate('temp_pembina_sampai', '>=', today()));

        return $milik->pluck('id');
    }

    private function milikEskuls(int $pembinaId)
    {
        $milik = Eskul::where('pembina_id', $pembinaId)->orWhere(fn ($q) => $q->where('temp_pembina_id', $pembinaId)->whereDate('temp_pembina_sampai', '>=', today()));

        return $milik->orderBy('name')->get();
    }

    private function pastikanMilik(ActivityPlan $kegiatan, $milikIds): void
    {
        if (! $milikIds->contains($kegiatan->eskul_id)) {
            abort(403);
        }
    }

    private function rules($milikIds): array
    {
        return [
            'eskul_id' => ['required', Rule::in($milikIds->all())],
            'tanggal' => ['required', 'date'],
            'jenis' => ['required', Rule::in(array_column(ActivityJenis::cases(), 'value'))],
            'agenda' => ['required', 'string', 'max:255'],
            'tujuan' => ['nullable', 'string'],
            'materi' => ['nullable', 'string'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'dokumentasi' => ['nullable', 'string', 'max:255'],
            'hasil' => ['nullable', 'string'],
            'kendala' => ['nullable', 'string'],
        ];
    }

    public function index(Request $request)
    {
        $me = $request->user();
        $milikIds = $this->milikIds($me->id);
        $eskuls = $this->milikEskuls($me->id);

        $eskulFilter = $request->query('eskul_id');
        $jenisFilter = $request->query('jenis');

        $kegiatans = ActivityPlan::with('eskul')
            ->whereIn('eskul_id', $milikIds)
            ->when($eskulFilter, fn ($q) => $q->where('eskul_id', $eskulFilter))
            ->when(in_array($jenisFilter, array_column(ActivityJenis::cases(), 'value'), true), fn ($q) => $q->where('jenis', $jenisFilter))
            ->orderBy('tanggal', 'desc')
            ->get();

        $scopeIds = $eskulFilter && $milikIds->contains($eskulFilter)
            ? collect([$eskulFilter])
            : $milikIds;
        $scopeNames = Eskul::whereIn('id', $scopeIds)->pluck('name');
        $periodeAktif = Periode::where('status', PeriodeStatus::DIBUKA)->pluck('periode');

        $anggota = Registration::where('status', Status::APPROVED)
            ->whereIn('periode', $periodeAktif)
            ->where(fn ($q) => $q->whereIn('eskul_utama', $scopeNames)->orWhereIn('eskul_cadangan', $scopeNames))
            ->count();

        $pertemuan = Kehadiran::whereIn('eskul_id', $scopeIds)
            ->selectRaw('eskul_id, tanggal, COUNT(*) as c, MAX(locked_at) as terkunci')
            ->groupBy('eskul_id', 'tanggal')
            ->orderByDesc('tanggal')
            ->take(12)
            ->get();

        $rekap = Kehadiran::whereIn('eskul_id', $scopeIds)
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status')
            ->all();

        $izinMenunggu = Izin::with('registration.user')
            ->where('status', IzinStatus::PENDING)
            ->whereIn('eskul_id', $scopeIds)
            ->orderBy('tanggal')
            ->take(20)
            ->get();

        $persenSiswa = Registration::with(['user', 'kehadiran'])
            ->where('status', Status::APPROVED)
            ->whereIn('periode', $periodeAktif)
            ->whereIn('eskul_utama', $scopeNames)
            ->orderBy('kelas')->orderBy('nis')
            ->take(30)
            ->get()
            ->map(function ($r) {
                $total = $r->kehadiran->count();
                $hadir = $r->kehadiran->where('status', KehadiranStatus::HADIR)->count();

                return [
                    'nama' => $r->user?->name ?? '—',
                    'persen' => $total > 0 ? round($hadir / $total * 100, 1) : 100,
                ];
            });

        return view('pembina.kegiatan.index', compact(
            'kegiatans', 'eskuls', 'eskulFilter', 'jenisFilter',
            'anggota', 'pertemuan', 'rekap', 'izinMenunggu', 'persenSiswa'
        ));
    }

    public function create(Request $request)
    {
        $eskuls = $this->milikEskuls($request->user()->id);

        return view('pembina.kegiatan.create', compact('eskuls'));
    }

    public function store(Request $request)
    {
        $me = $request->user();
        $milikIds = $this->milikIds($me->id);

        $data = $request->validate($this->rules($milikIds));
        $data['created_by'] = $me->email;

        $kegiatan = ActivityPlan::create($data);

        AdminLog::record('pembina.kegiatan.store', ActivityPlan::class, $kegiatan->id, "Tambah kegiatan {$data['agenda']} ({$data['jenis']})");

        return redirect()->route('pembina.kegiatan.index')->with('status', 'Kegiatan berhasil ditambahkan.');
    }

    public function show(Request $request, ActivityPlan $kegiatan)
    {
        $this->pastikanMilik($kegiatan, $this->milikIds($request->user()->id));

        $kegiatan->loadMissing('eskul');

        return view('pembina.kegiatan.show', compact('kegiatan'));
    }

    public function edit(Request $request, ActivityPlan $kegiatan)
    {
        $this->pastikanMilik($kegiatan, $this->milikIds($request->user()->id));

        $eskuls = $this->milikEskuls($request->user()->id);

        return view('pembina.kegiatan.edit', compact('kegiatan', 'eskuls'));
    }

    public function update(Request $request, ActivityPlan $kegiatan)
    {
        $me = $request->user();
        $milikIds = $this->milikIds($me->id);

        $this->pastikanMilik($kegiatan, $milikIds);

        $data = $request->validate($this->rules($milikIds));

        $kegiatan->update($data);

        AdminLog::record('pembina.kegiatan.update', ActivityPlan::class, $kegiatan->id, "Ubah kegiatan {$kegiatan->agenda}");

        return redirect()->route('pembina.kegiatan.show', $kegiatan)->with('status', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Request $request, ActivityPlan $kegiatan)
    {
        $this->pastikanMilik($kegiatan, $this->milikIds($request->user()->id));

        $info = "{$kegiatan->agenda} ({$kegiatan->jenis->value})";
        $kegiatan->delete();

        AdminLog::record('pembina.kegiatan.destroy', ActivityPlan::class, $kegiatan->id, "Hapus kegiatan {$info}");

        return redirect()->route('pembina.kegiatan.index')->with('status', 'Kegiatan berhasil dihapus.');
    }
}
