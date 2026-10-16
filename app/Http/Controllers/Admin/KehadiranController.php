<?php

namespace App\Http\Controllers\Admin;

use App\Enums\KehadiranStatus;
use App\Enums\PeriodeStatus;
use App\Enums\Role;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Eskul;
use App\Models\Kehadiran;
use App\Models\Periode;
use App\Models\Registration;
use App\Support\CsvExport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KehadiranController extends Controller
{
    public function index(Request $request)
    {
        $eskuls = Eskul::orderBy('name')->get();

        if (auth()->user()->hasRole(Role::PEMBINA)) {
            $eskuls = $this->eskulPembina(auth()->id());
        }

        $eskulId = $request->query('eskul_id', $eskuls->first()?->id);
        $tanggal = $request->query('tanggal', today()->toDateString());

        $eskul = $eskuls->firstWhere('id', (int) $eskulId) ?? $eskuls->first();

        $periodeAktif = Periode::where('status', PeriodeStatus::DIBUKA)->orderByDesc('periode')->first();

        $pendaftar = collect();
        $peta = collect();
        $locked = false;
        $rombelStats = collect();

        if ($eskul) {
            $q = Registration::with('user')
                ->join('users', 'users.id', '=', 'registrations.user_id')
                ->select('registrations.*')
                ->where('status', Status::APPROVED)
                ->where('eskul_utama', $eskul->name);

            if ($periodeAktif) {
                $q->where('periode', $periodeAktif->periode);
            }

            $pendaftar = $q->get()->sort(function ($a, $b) {
                $tingkatA = \App\Support\Kelas::tingkat($a->kelas) ?? 99;
                $tingkatB = \App\Support\Kelas::tingkat($b->kelas) ?? 99;
                if ($tingkatA !== $tingkatB) {
                    return $tingkatA <=> $tingkatB;
                }
                $cmp = strnatcasecmp($a->kelas ?? '', $b->kelas ?? '');
                if ($cmp !== 0) {
                    return $cmp;
                }
                return strcasecmp($a->user?->name ?? '', $b->user?->name ?? '');
            })->values();

            $peta = Kehadiran::where('eskul_id', $eskul->id)
                ->whereDate('tanggal', $tanggal)
                ->get()->keyBy('registration_id');

            $locked = $peta->isNotEmpty() && $peta->every(fn ($k) => $k->isLocked());

            $rombelStats = $pendaftar->groupBy('kelas')->map(function ($members, $kelasName) use ($peta) {
                $total = $members->count();
                $terisi = 0;
                $hadir = 0;
                foreach ($members as $m) {
                    $st = $peta[$m->id]?->status?->value ?? null;
                    if ($st !== null) {
                        $terisi++;
                        if ($st === 'HADIR') {
                            $hadir++;
                        }
                    }
                }
                $belum = $total - $terisi;
                return [
                    'kelas' => $kelasName,
                    'total' => $total,
                    'hadir' => $hadir,
                    'terisi' => $terisi,
                    'belum' => $belum,
                    'lengkap' => $belum === 0 && $total > 0,
                ];
            });
        }

        return view('admin.kehadiran.index', [
            'eskuls' => $eskuls,
            'eskul' => $eskul,
            'tanggal' => $tanggal,
            'pendaftar' => $pendaftar,
            'peta' => $peta,
            'locked' => $locked,
            'rombelStats' => $rombelStats,
            'statusList' => KehadiranStatus::cases(),
            'periodeAktif' => $periodeAktif,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'eskul_id' => ['required', 'exists:eskuls,id'],
            'tanggal' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:'.now()->subYear()->toDateString()],
            'status' => ['required', 'array'],
            'status.*' => ['required', 'in:HADIR,IZIN,SAKIT,ALPA'],
            'keterangan' => ['nullable', 'array'],
            'keterangan.*' => ['nullable', 'string', 'max:200'],
        ]);

        $eskul = Eskul::findOrFail($data['eskul_id']);
        $this->pastikanAksesEskul($eskul->id);

        // Hanya pendaftaran APPROVED milik eskul ini yang boleh ditulis.
        $bolehIds = \App\Models\Registration::where('eskul_utama', $eskul->name)
            ->where('status', \App\Enums\Status::APPROVED)
            ->pluck('id')->map(fn ($v) => (string) $v)->all();
        $boleh = array_flip($bolehIds);

        $diabaikan = 0;
        $disimpan = 0;
        $ditolak = 0;

        foreach ($data['status'] as $registrationId => $status) {
            if (! isset($boleh[(string) $registrationId])) {
                $ditolak++;
                continue;
            }
            $existing = Kehadiran::where('eskul_id', $eskul->id)
                ->where('registration_id', $registrationId)
                ->whereDate('tanggal', $data['tanggal'])
                ->first();

            if ($existing && $existing->isLocked()) {
                $diabaikan++;
                continue;
            }

            Kehadiran::updateOrCreate(
                [
                    'eskul_id' => $eskul->id,
                    'registration_id' => $registrationId,
                    'tanggal' => $data['tanggal'],
                ],
                [
                    'status' => $status,
                    'keterangan' => $data['keterangan'][$registrationId] ?? null,
                ]
            );
            $disimpan++;
        }

        AdminLog::record('kehadiran.store', 'Kehadiran', (string) $eskul->id, "Simpan kehadiran {$eskul->name} {$data['tanggal']}: {$disimpan} disimpan, {$diabaikan} diabaikan (terkunci).");

        $pesan = "Kehadiran tersimpan ({$disimpan} baris, {$diabaikan} terkunci diabaikan).";
        if ($ditolak > 0) {
            $pesan .= " {$ditolak} baris ditolak (bukan anggota eskul ini).";
        }

        return back()->with('status', $pesan);
    }

    public function lock(Request $request)
    {
        $data = $request->validate([
            'eskul_id' => ['required', 'exists:eskuls,id'],
            'tanggal' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:'.now()->subYear()->toDateString()],
        ]);

        // Pembina boleh mengesahkan eskul ampuannya; membuka kunci khusus admin/operator.
        if (auth()->user()->hasRole(Role::PEMBINA)) {
            $this->pastikanAksesEskul((int) $data['eskul_id']);
        } elseif (! auth()->user()->hasRole(Role::ADMIN, Role::OPERATOR)) {
            abort(403);
        }

        $count = Kehadiran::where('eskul_id', $data['eskul_id'])
            ->whereDate('tanggal', $data['tanggal'])
            ->whereNull('locked_at')
            ->update([
                'locked_at' => now(),
                'locked_by' => auth()->user()->email,
            ]);

        \App\Models\Pertemuan::where('eskul_id', $data['eskul_id'])
            ->whereDate('tanggal', $data['tanggal'])
            ->whereNull('locked_at')
            ->update([
                'locked_at' => now(),
                'locked_by' => auth()->user()->email,
            ]);

        AdminLog::record('kehadiran.lock', 'Kehadiran', (string) $data['eskul_id'], "Kunci kehadiran eskul #{$data['eskul_id']} tanggal {$data['tanggal']} ({$count} baris).");

        return back()->with('status', "Kehadiran tanggal {$data['tanggal']} dikunci ({$count} baris).");
    }

    public function unlock(Request $request)
    {
        if (! auth()->user()->hasRole(Role::ADMIN, Role::OPERATOR)) {
            abort(403);
        }

        $data = $request->validate([
            'eskul_id' => ['required', 'exists:eskuls,id'],
            'tanggal' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:'.now()->subYear()->toDateString()],
        ]);

        $count = Kehadiran::where('eskul_id', $data['eskul_id'])
            ->whereDate('tanggal', $data['tanggal'])
            ->whereNotNull('locked_at')
            ->update([
                'locked_at' => null,
                'locked_by' => null,
            ]);

        \App\Models\Pertemuan::where('eskul_id', $data['eskul_id'])
            ->whereDate('tanggal', $data['tanggal'])
            ->update([
                'locked_at' => null,
                'locked_by' => null,
            ]);

        AdminLog::record('kehadiran.unlock', 'Kehadiran', (string) $data['eskul_id'], "Buka kunci kehadiran eskul #{$data['eskul_id']} tanggal {$data['tanggal']} ({$count} baris).");

        return back()->with('status', "Kunci kehadiran tanggal {$data['tanggal']} dibuka ({$count} baris).");
    }

    private function eskulPembina(int $userId)
    {
        return Eskul::where('pembina_id', $userId)
            ->orWhere(function ($q) use ($userId) {
                $q->where('temp_pembina_id', $userId)
                    ->whereDate('temp_pembina_sampai', '>=', today());
            })
            ->orderBy('name')->get();
    }

    private function pastikanAksesEskul(int $eskulId): void
    {
        if (! auth()->user()->hasRole(Role::PEMBINA)) {
            return;
        }

        $boleh = $this->eskulPembina(auth()->id())->contains('id', $eskulId);

        if (! $boleh) {
            abort(403, 'Anda bukan pembina eskul ini.');
        }
    }
}
