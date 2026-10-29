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
    public function index(Request $request)
    {
        return redirect()->route('admin.periodes.index', $request->query());
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

        return redirect()->route('admin.periodes.index', ['periode' => $periodeModel->periode])->with('status', 'Jendela pendaftaran berhasil diperbarui.');
    }
}
