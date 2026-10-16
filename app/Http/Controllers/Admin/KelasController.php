<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Jurusan;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Periode;
use App\Models\Registration;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Support\Kelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $periodes = Periode::orderByDesc('periode')->get();
        $aktif = Periode::where('status', \App\Enums\PeriodeStatus::DIBUKA)->orderByDesc('periode')->first();
        $periode = $request->query('periode', $aktif?->periode ?? $periodes->first()?->periode);

        if ($periode && ! preg_match('/^\d{4}\/\d{4}$/', $periode)) {
            $periode = $aktif?->periode ?? $periodes->first()?->periode;
        }

        $regs = Registration::with('user')
            ->when($periode, fn ($q) => $q->where('periode', $periode))
            ->get();

        $jurusans = Jurusan::cases();

        // Master Data Classes
        $masterKelas = SchoolClass::with('waliKelas')->get()->sort(function ($a, $b) {
            $ta = Kelas::tingkat($a->nama) ?? 99;
            $tb = Kelas::tingkat($b->nama) ?? 99;
            if ($ta !== $tb) {
                return $ta <=> $tb;
            }
            return strnatcasecmp($a->nama, $b->nama);
        })->values();

        $waliUsers = User::whereIn('role', [Role::WALI_KELAS, Role::PEMBINA, Role::OPERATOR, Role::ADMIN])
            ->orderBy('name')
            ->get();

        $kelasList = $regs->pluck('kelas')->filter()->unique()
            ->sortBy(fn ($k) => $this->urutKelas($k))->values();

        $matrix = [];
        foreach ($kelasList as $k) {
            foreach ($jurusans as $j) {
                $matrix[$k][$j->value] = $regs
                    ->filter(fn ($r) => $r->kelas === $k && $r->jurusan?->value === $j->value)
                    ->count();
            }
        }

        $rincian = collect($jurusans)->mapWithKeys(function ($j) use ($regs) {
            $milik = $regs->filter(fn ($r) => $r->jurusan?->value === $j->value);

            return [$j->value => [
                'jurusan' => $j,
                'total' => $milik->count(),
                'perKelas' => $milik->groupBy('kelas')->sortKeysUsing(fn ($a, $b) => strcmp($this->urutKelas($a), $this->urutKelas($b))),
            ]];
        });

        return view('admin.kelas.index', compact(
            'periodes',
            'periode',
            'jurusans',
            'kelasList',
            'matrix',
            'rincian',
            'masterKelas',
            'waliUsers'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:50', 'unique:school_classes,nama'],
            'tingkat' => ['nullable', 'integer', 'min:10', 'max:13'],
            'jurusan' => ['nullable', 'string', 'max:20'],
            'wali_kelas_nama' => ['nullable', 'string', 'max:100'],
            'wali_kelas_id' => ['nullable', 'exists:users,id'],
        ]);

        $data['nama'] = Kelas::normalisasi($data['nama']);
        if (empty($data['tingkat'])) {
            $data['tingkat'] = Kelas::tingkat($data['nama']);
        }

        if (! empty($data['wali_kelas_id'])) {
            $user = User::find($data['wali_kelas_id']);
            if ($user && $user->role === Role::STUDENT) {
                $user->update(['role' => Role::WALI_KELAS]);
            }
            if (empty($data['wali_kelas_nama']) && $user) {
                $data['wali_kelas_nama'] = $user->name;
            }
        }

        SchoolClass::create($data);

        return back()->with('status', "Rombel kelas {$data['nama']} berhasil ditambahkan ke master.");
    }

    public function update(Request $request, SchoolClass $schoolClass): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:50', Rule::unique('school_classes', 'nama')->ignore($schoolClass->id)],
            'tingkat' => ['nullable', 'integer', 'min:10', 'max:13'],
            'jurusan' => ['nullable', 'string', 'max:20'],
            'wali_kelas_nama' => ['nullable', 'string', 'max:100'],
            'wali_kelas_id' => ['nullable', 'exists:users,id'],
        ]);

        $data['nama'] = Kelas::normalisasi($data['nama']);
        if (empty($data['tingkat'])) {
            $data['tingkat'] = Kelas::tingkat($data['nama']);
        }

        if (! empty($data['wali_kelas_id'])) {
            $user = User::find($data['wali_kelas_id']);
            if ($user && ! in_array($user->role, [Role::ADMIN, Role::OPERATOR], true)) {
                $user->update(['role' => Role::WALI_KELAS]);
            }
            if (empty($data['wali_kelas_nama']) && $user) {
                $data['wali_kelas_nama'] = $user->name;
            }
        }

        $schoolClass->update($data);

        return back()->with('status', "Data rombel kelas {$schoolClass->nama} berhasil diperbarui.");
    }

    public function destroy(SchoolClass $schoolClass): RedirectResponse
    {
        $nama = $schoolClass->nama;
        $schoolClass->delete();

        return back()->with('status', "Rombel kelas {$nama} berhasil dihapus dari master.");
    }

    public function syncFromStudents(): RedirectResponse
    {
        $distinctClasses = Student::select('kelas', 'jurusan')
            ->distinct()
            ->whereNotNull('kelas')
            ->get();

        $added = 0;
        foreach ($distinctClasses as $ds) {
            $clean = Kelas::normalisasi($ds->kelas);
            if (! $clean) {
                continue;
            }
            $tingkat = Kelas::tingkat($clean);
            $jurusan = $ds->jurusan?->value ?? (preg_match('/(RPL|DKV|KULINER|TP|TKP)/i', $clean, $m) ? strtoupper($m[1]) : null);

            $sc = SchoolClass::firstOrCreate(
                ['nama' => $clean],
                [
                    'tingkat' => $tingkat,
                    'jurusan' => $jurusan,
                    'wali_kelas_nama' => Student::where('kelas', $ds->kelas)->whereNotNull('wali_nama')->value('wali_nama'),
                ]
            );
            if ($sc->wasRecentlyCreated) {
                $added++;
            }
        }

        return back()->with('status', "Sinkronisasi selesai. {$added} rombel baru ditambahkan ke Master Kelas.");
    }

    private function urutKelas(string $k): string
    {
        if (preg_match('/^(\d+)\s*(.*)$/', $k, $m)) {
            return sprintf('%02d-%s', (int) $m[1], $m[2]);
        }

        return '99-'.$k;
    }
}
