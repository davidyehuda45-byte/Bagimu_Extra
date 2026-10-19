<?php

namespace App\Http\Controllers\Pembina;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Eskul;
use App\Models\Kehadiran;
use Illuminate\Http\Request;

class KehadiranController extends Controller
{
    private function pastikanMilik(Eskul $eskul, int $pembinaId): void
    {
        $isMilik = $eskul->pembina_id === $pembinaId
            || ($eskul->temp_pembina_id === $pembinaId
                && $eskul->temp_pembina_sampai
                && $eskul->temp_pembina_sampai->gte(today()));

        if (! $isMilik) {
            abort(403);
        }
    }

    public function store(Request $request, Eskul $eskul)
    {
        $this->pastikanMilik($eskul, $request->user()->id);

        $data = $request->validate([
            'tanggal' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:'.now()->subYear()->toDateString()],
            'status' => ['required', 'array'],
            'status.*' => ['required', 'in:HADIR,IZIN,SAKIT,ALPA'],
            'keterangan' => ['nullable', 'array'],
            'keterangan.*' => ['nullable', 'string', 'max:200'],
        ]);

        $bolehIds = \App\Models\Registration::where('eskul_utama', $eskul->name)
            ->where('status', \App\Enums\Status::APPROVED)
            ->pluck('id')->map(fn ($v) => (string) $v)->all();
        $boleh = array_flip($bolehIds);

        $disimpan = 0;
        $diabaikan = 0;
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

            if ($existing) {
                $existing->update([
                    'status' => $status,
                    'keterangan' => $data['keterangan'][$registrationId] ?? null,
                ]);
            } else {
                Kehadiran::create([
                    'eskul_id' => $eskul->id,
                    'registration_id' => $registrationId,
                    'tanggal' => $data['tanggal'],
                    'status' => $status,
                    'keterangan' => $data['keterangan'][$registrationId] ?? null,
                ]);
            }
            $disimpan++;
        }

        AdminLog::record('pembina.kehadiran.store', 'Kehadiran', (string) $eskul->id, "Simpan kehadiran {$eskul->name} {$data['tanggal']}: {$disimpan} disimpan.");

        $pesan = "Kehadiran tersimpan ({$disimpan} baris).";
        if ($ditolak > 0) {
            $pesan .= " {$ditolak} baris ditolak (bukan anggota eskul ini).";
        }

        return back()->with('status', $pesan);
    }

    /**
     * Sahkan (kunci) absensi eskul ampuan. Membuka kunci khusus admin.
     */
    public function lock(Request $request, Eskul $eskul)
    {
        $this->pastikanMilik($eskul, $request->user()->id);

        $data = $request->validate([
            'tanggal' => ['required', 'date'],
        ]);

        $count = Kehadiran::where('eskul_id', $eskul->id)
            ->whereDate('tanggal', $data['tanggal'])
            ->whereNull('locked_at')
            ->update([
                'locked_at' => now(),
                'locked_by' => $request->user()->email,
            ]);

        \App\Models\Pertemuan::where('eskul_id', $eskul->id)
            ->whereDate('tanggal', $data['tanggal'])
            ->whereNull('locked_at')
            ->update([
                'locked_at' => now(),
                'locked_by' => $request->user()->email,
            ]);

        AdminLog::record('pembina.kehadiran.lock', 'Kehadiran', (string) $eskul->id, "Sahkan kehadiran {$eskul->name} {$data['tanggal']} ({$count} baris).");

        return back()->with('status', "Kehadiran tanggal {$data['tanggal']} disahkan ({$count} baris).");
    }
}
