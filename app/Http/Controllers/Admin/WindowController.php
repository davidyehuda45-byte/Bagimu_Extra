<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PeriodeStatus;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\PendaftaranWindow;
use App\Models\Periode;
use Illuminate\Http\Request;

class WindowController extends Controller
{
    public function index()
    {
        $periodes = Periode::with(['window'])->withCount('registrations')->orderByDesc('periode')->paginate(15);
        $activePeriode = Periode::where('status', PeriodeStatus::DIBUKA)->orderByDesc('periode')->first()
            ?? Periode::orderByDesc('periode')->first();
        $window = $activePeriode ? PendaftaranWindow::firstOrNew(['periode' => $activePeriode->periode]) : null;

        return view('admin.periodes.index', compact('periodes', 'activePeriode', 'window'));
    }

    public function update(Request $request, string $periode)
    {
        $periodeModel = Periode::findOrFail($periode);

        $data = $request->validate([
            'buka' => ['nullable', 'boolean'],
            'tgl_buka' => ['nullable', 'date'],
            'tgl_tutup' => ['nullable', 'date', 'after:tgl_buka'],
        ]);

        PendaftaranWindow::updateOrCreate(
            ['periode' => $periodeModel->periode],
            [
                'buka' => $request->boolean('buka'),
                'tgl_buka' => $data['tgl_buka'] ?? null,
                'tgl_tutup' => $data['tgl_tutup'] ?? null,
            ]
        );

        AdminLog::record('window.update', 'pendaftaran_window', $periodeModel->periode, "Mengubah jendela pendaftaran {$periodeModel->periode}");

        return back()->with('status', 'Jendela pendaftaran berhasil diperbarui.');
    }
}
