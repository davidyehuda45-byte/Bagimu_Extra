<?php

namespace App\Http\Controllers\Pembina;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Eskul;
use App\Models\EskulJadwal;
use Illuminate\Http\Request;

class JadwalController extends Controller
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
            'hari' => ['required', 'in:SENIN,SELASA,RABU,KAMIS,JUMAT,SABTU,MINGGU'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'tempat' => ['required', 'string', 'max:100'],
        ]);

        if ($this->bentrok($eskul, $data['hari'], $data['jam_mulai'], $data['jam_selesai'])) {
            return back()->withInput()->withErrors([
                'hari' => 'Jadwal bentrok dengan jadwal lain di hari yang sama.',
            ]);
        }

        $jadwal = $eskul->jadwals()->create($data);

        AdminLog::record('pembina.jadwal.store', EskulJadwal::class, $jadwal->id, "Tambah jadwal {$eskul->name} {$data['hari']} {$data['jam_mulai']}-{$data['jam_selesai']}");

        return redirect()->route('pembina.eskul.show', $eskul)->with('status', 'Jadwal berhasil ditambahkan.');
    }

    public function update(Request $request, EskulJadwal $jadwal)
    {
        $jadwal->loadMissing('eskul');
        $this->pastikanMilik($jadwal->eskul, $request->user()->id);

        $data = $request->validate([
            'hari' => ['required', 'in:SENIN,SELASA,RABU,KAMIS,JUMAT,SABTU,MINGGU'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'tempat' => ['required', 'string', 'max:100'],
        ]);

        if ($this->bentrok($jadwal->eskul, $data['hari'], $data['jam_mulai'], $data['jam_selesai'], $jadwal->id)) {
            return back()->withInput()->withErrors([
                'hari' => 'Jadwal bentrok dengan jadwal lain di hari yang sama.',
            ]);
        }

        $jadwal->update($data);

        AdminLog::record('pembina.jadwal.update', EskulJadwal::class, $jadwal->id, "Ubah jadwal {$jadwal->eskul->name} {$data['hari']} {$data['jam_mulai']}-{$data['jam_selesai']}");

        return redirect()->route('pembina.eskul.show', $jadwal->eskul_id)->with('status', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Request $request, EskulJadwal $jadwal)
    {
        $jadwal->loadMissing('eskul');

        $this->pastikanMilik($jadwal->eskul, $request->user()->id);

        $eskulId = $jadwal->eskul_id;
        $info = "{$jadwal->eskul->name} {$jadwal->hari} {$jadwal->jam_mulai}-{$jadwal->jam_selesai}";
        $jadwal->delete();

        AdminLog::record('pembina.jadwal.destroy', EskulJadwal::class, $jadwal->id, "Hapus jadwal {$info}");

        return redirect()->route('pembina.eskul.show', $eskulId)->with('status', 'Jadwal berhasil dihapus.');
    }

    private function bentrok(Eskul $eskul, string $hari, string $mulai, string $selesai, ?int $kecualiId = null): bool
    {
        return $eskul->jadwals()
            ->where('hari', $hari)
            ->when($kecualiId, fn ($q) => $q->where('id', '!=', $kecualiId))
            ->where('jam_mulai', '<', $selesai)
            ->where('jam_selesai', '>', $mulai)
            ->exists();
    }
}
