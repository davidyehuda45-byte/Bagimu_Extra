<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PeriodeStatus;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PeriodeController extends Controller
{
    public function index()
    {
        $periodes = Periode::with(['window'])->withCount('registrations')->orderByDesc('periode')->paginate(15);

        return view('admin.periodes.index', compact('periodes'));
    }

    public function create()
    {
        $statusList = PeriodeStatus::cases();

        return view('admin.periodes.create', compact('statusList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'periode' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/', 'unique:periodes,periode'],
            'nama' => ['nullable', 'string', 'max:100'],
            'tahun_ajaran' => ['nullable', 'string', 'max:9'],
            'semester' => ['nullable', 'string', 'max:20'],
            'tgl_mulai' => ['nullable', 'date'],
            'tgl_selesai' => ['nullable', 'date', 'after_or_equal:tgl_mulai'],
            'status' => ['required', Rule::enum(PeriodeStatus::class)],
            'max_eskul_per_siswa' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        DB::transaction(function () use ($data) {
            if (($data['status'] ?? null) === PeriodeStatus::DIBUKA->value) {
                Periode::where('status', PeriodeStatus::DIBUKA->value)->update(['status' => PeriodeStatus::DITUTUP->value]);
            }

            Periode::create([
                'periode' => $data['periode'],
                'nama' => $data['nama'] ?? null,
                'tahun_ajaran' => $data['tahun_ajaran'] ?? null,
                'semester' => $data['semester'] ?? null,
                'tgl_mulai' => $data['tgl_mulai'] ?? null,
                'tgl_selesai' => $data['tgl_selesai'] ?? null,
                'status' => $data['status'],
                'max_eskul_per_siswa' => $data['max_eskul_per_siswa'],
            ]);
        });

        AdminLog::record('periode.create', 'periode', $data['periode'], "Membuat periode {$data['periode']}");

        return redirect()->route('admin.periodes.index')->with('status', 'Periode berhasil dibuat.');
    }

    public function show(string $periode)
    {
        $periodeModel = Periode::with('window')->findOrFail($periode);

        return view('admin.periodes.show', ['periode' => $periodeModel]);
    }

    public function edit(string $periode)
    {
        $periodeModel = Periode::with('window')->findOrFail($periode);
        $statusList = PeriodeStatus::cases();

        return view('admin.periodes.edit', ['periode' => $periodeModel, 'statusList' => $statusList]);
    }

    public function update(Request $request, string $periode)
    {
        $periodeModel = Periode::findOrFail($periode);

        $data = $request->validate([
            'nama' => ['nullable', 'string', 'max:100'],
            'tahun_ajaran' => ['nullable', 'string', 'max:9'],
            'semester' => ['nullable', 'string', 'max:20'],
            'tgl_mulai' => ['nullable', 'date'],
            'tgl_selesai' => ['nullable', 'date'],
            'status' => ['required', Rule::enum(PeriodeStatus::class)],
            'max_eskul_per_siswa' => ['required', 'integer', 'min:1'],
        ]);

        $dari = $periodeModel->status->value;
        $ke = $data['status'] instanceof PeriodeStatus ? $data['status']->value : $data['status'];

        $boleh = [
            'DRAFT' => ['TERJADWAL', 'DIBUKA', 'DIARSIPKAN'],
            'TERJADWAL' => ['DIBUKA', 'DITUTUP', 'DRAFT'],
            'DIBUKA' => ['DITUTUP', 'DIARSIPKAN'],
            'DITUTUP' => ['DIBUKA', 'DIARSIPKAN', 'TERJADWAL'],
            'DIARSIPKAN' => [],
        ];

        if ($ke !== $dari) {
            if (! auth()->user()->isAdmin()) {
                return back()->withErrors(['status' => 'Hanya admin yang boleh mengubah status periode.']);
            }

            if (! in_array($ke, $boleh[$dari] ?? [], true)) {
                return back()->withErrors(['status' => "Transisi {$dari} → {$ke} tidak diizinkan."]);
            }
        }

        DB::transaction(function () use ($data, $ke, $dari, $periodeModel) {
            if ($ke === PeriodeStatus::DIBUKA->value && $dari !== PeriodeStatus::DIBUKA->value) {
                Periode::where('status', PeriodeStatus::DIBUKA->value)
                    ->where('periode', '!=', $periodeModel->periode)
                    ->update(['status' => PeriodeStatus::DITUTUP->value]);
            }

            $periodeModel->update($data);
        });

        AdminLog::record('periode.update', 'periode', $periodeModel->periode, "Mengubah periode {$periodeModel->periode}");

        return redirect()->route('admin.periodes.show', $periodeModel->periode)->with('status', 'Periode berhasil diperbarui.');
    }

    public function destroy(string $periode)
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Hanya admin yang boleh menghapus periode.');

        $periodeModel = Periode::findOrFail($periode);

        if ($periodeModel->status === PeriodeStatus::DIBUKA) {
            return redirect()->route('admin.periodes.index')->withErrors(['periode' => 'Periode aktif tidak dapat dihapus.']);
        }

        // Arsip alumni: hapus data periode, akun user tetap.
        DB::transaction(function () use ($periodeModel) {
            \App\Models\Registration::where('periode', $periodeModel->periode)->delete();
            \App\Models\PendaftaranWindow::where('periode', $periodeModel->periode)->delete();
            $periodeModel->delete();
        });

        AdminLog::record('periode.delete', 'periode', $periode, "Menghapus data periode {$periode} (arsip).");

        return redirect()->route('admin.periodes.index')->with('status', 'Data periode dihapus (akun user tetap).');
    }
}
