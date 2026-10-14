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
        $periode = Periode::where('status', PeriodeStatus::DIBUKA)->orderByDesc('periode')->first()
            ?? Periode::orderByDesc('periode')->first();

        $window = $periode ? PendaftaranWindow::firstOrNew(['periode' => $periode->periode]) : null;

        return view('admin.windows.index', compact('periode', 'window'));
    }

    public function update(Request $request, string $periode)
    {
        $periodeModel = Periode::findOrFail($periode);

        $data = $request->validate([
            'buka' => ['nullable', 'boolean'],
            'tgl_buka' => ['nullable', 'date'],
            'tgl_tutup' => ['nullable', 'date'],
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
