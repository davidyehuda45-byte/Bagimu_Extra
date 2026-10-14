<?php

namespace App\Http\Controllers\Pembina;

use App\Enums\IzinStatus;
use App\Enums\KehadiranStatus;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Eskul;
use App\Models\Izin;
use App\Models\Kehadiran;
use Illuminate\Http\Request;

class IzinController extends Controller
{
    private function milikIds(int $pembinaId)
    {
        return Eskul::where('pembina_id', $pembinaId)
            ->orWhere(fn ($q) => $q->where('temp_pembina_id', $pembinaId)->whereDate('temp_pembina_sampai', '>=', today()))
            ->pluck('id');
    }

    public function approve(Request $request, Izin $izin)
    {
        abort_unless($this->milikIds($request->user()->id)->contains($izin->eskul_id), 403);

        $izin->update([
            'status' => IzinStatus::APPROVED,
            'decided_by' => $request->user()->email,
            'decided_at' => now(),
        ]);

        $baris = Kehadiran::where('eskul_id', $izin->eskul_id)
            ->where('registration_id', $izin->registration_id)
            ->whereDate('tanggal', $izin->tanggal)
            ->first();

        if (! $baris || ! $baris->isLocked()) {
            Kehadiran::updateOrCreate(
                [
                    'eskul_id' => $izin->eskul_id,
                    'registration_id' => $izin->registration_id,
                    'tanggal' => $izin->tanggal,
                ],
                [
                    'status' => $izin->jenis->value === 'SAKIT' ? KehadiranStatus::SAKIT : KehadiranStatus::IZIN,
                    'keterangan' => 'Izin disetujui: '.$izin->alasan,
                ]
            );
        }

        AdminLog::record('pembina.izin.approve', 'Izin', (string) $izin->id, "Izin #{$izin->id} disetujui pembina.");

        return back()->with('status', 'Pengajuan izin disetujui.');
    }

    public function reject(Request $request, Izin $izin)
    {
        abort_unless($this->milikIds($request->user()->id)->contains($izin->eskul_id), 403);

        $izin->update([
            'status' => IzinStatus::REJECTED,
            'decided_by' => $request->user()->email,
            'decided_at' => now(),
        ]);

        AdminLog::record('pembina.izin.reject', 'Izin', (string) $izin->id, "Izin #{$izin->id} ditolak pembina.");

        return back()->with('status', 'Pengajuan izin ditolak.');
    }
}
