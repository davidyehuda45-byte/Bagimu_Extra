<?php

namespace App\Http\Controllers\Admin;

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
    public function index(Request $request)
    {
        $eskuls = Eskul::orderBy('name')->get();

        $query = Izin::with(['registration.user', 'eskul'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('eskul_id')) {
            $query->where('eskul_id', $request->query('eskul_id'));
        }

        $izin = $query->paginate(20)->withQueryString();

        return view('admin.izin.index', [
            'izin' => $izin,
            'eskuls' => $eskuls,
            'filterStatus' => $request->query('status', ''),
            'filterEskul' => $request->query('eskul_id', ''),
            'statusList' => IzinStatus::cases(),
        ]);
    }

    public function approve(Izin $izin)
    {
        $izin->update([
            'status' => IzinStatus::APPROVED,
            'decided_by' => auth()->user()->email,
            'decided_at' => now(),
        ]);

        // Sinkron ke presensi: SAKIT→SAKIT, lainnya→IZIN (DISPENSASI tidak ada di enum hadir,
        // jadi disimpan sebagai IZIN dengan keterangan jenis asli). Abaikan bila terkunci.
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
                    'keterangan' => $izin->jenis->label().' disetujui: '.$izin->alasan,
                ]
            );
        }

        AdminLog::record('izin.approve', 'Izin', (string) $izin->id, "Izin #{$izin->id} disetujui.");

        return back()->with('status', 'Pengajuan izin disetujui.');
    }

    public function reject(Request $request, Izin $izin)
    {
        $izin->update([
            'status' => IzinStatus::REJECTED,
            'decided_by' => auth()->user()->email,
            'decided_at' => now(),
        ]);

        // Bersihkan baris hadir otomatis dari approve sebelumnya (bila belum terkunci),
        // agar tidak ada status IZIN/SAKIT basi setelah ditolak.
        $baris = Kehadiran::where('eskul_id', $izin->eskul_id)
            ->where('registration_id', $izin->registration_id)
            ->whereDate('tanggal', $izin->tanggal)
            ->first();

        if ($baris && ! $baris->isLocked()) {
            $ket = (string) $baris->keterangan;
            if (str_starts_with($ket, 'Izin disetujui:') || str_starts_with($ket, 'Sakit disetujui:') || str_starts_with($ket, 'Dispensasi disetujui:')) {
                $baris->delete();
            }
        }

        AdminLog::record('izin.reject', 'Izin', (string) $izin->id, "Izin #{$izin->id} ditolak.");

        return back()->with('status', 'Pengajuan izin ditolak.');
    }
}
