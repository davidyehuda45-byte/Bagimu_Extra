<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Eskul;
use App\Models\EskulJadwal;
use Illuminate\Http\Request;

class EskulJadwalController extends Controller
{
    public function store(Request $request, Eskul $eskul)
    {
        $data = $request->validate([
            'hari' => ['required', 'in:SENIN,SELASA,RABU,KAMIS,JUMAT,SABTU,MINGGU'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'tempat' => ['required', 'string', 'max:100'],
        ]);

        $bentrok = $eskul->jadwals()
            ->where('hari', $data['hari'])
            ->where('jam_mulai', '<', $data['jam_selesai'])
            ->where('jam_selesai', '>', $data['jam_mulai'])
            ->exists();

        if ($bentrok) {
            return back()->withInput()->withErrors([
                'hari' => 'Jadwal bentrok dengan jadwal lain di hari yang sama.',
            ]);
        }

        $jadwal = $eskul->jadwals()->create($data);

        AdminLog::record('jadwal.create', 'eskul_jadwal', $jadwal->id, "Tambah jadwal {$eskul->name} {$data['hari']}");

        return redirect()->route('admin.eskuls.show', $eskul)->with('status', 'Jadwal berhasil ditambahkan.');
    }

    public function update(Request $request, EskulJadwal $jadwal)
    {
        $data = $request->validate([
            'hari' => ['required', 'in:SENIN,SELASA,RABU,KAMIS,JUMAT,SABTU,MINGGU'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'tempat' => ['required', 'string', 'max:100'],
        ]);

        $bentrok = EskulJadwal::where('eskul_id', $jadwal->eskul_id)
            ->where('id', '!=', $jadwal->id)
            ->where('hari', $data['hari'])
            ->where('jam_mulai', '<', $data['jam_selesai'])
            ->where('jam_selesai', '>', $data['jam_mulai'])
            ->exists();

        if ($bentrok) {
            return back()->withInput()->withErrors([
                'hari' => 'Jadwal bentrok dengan jadwal lain di hari yang sama.',
            ]);
        }

        $jadwal->update($data);
        $jadwal->loadMissing('eskul');

        AdminLog::record('jadwal.update', 'eskul_jadwal', $jadwal->id, "Ubah jadwal {$jadwal->eskul?->name} {$data['hari']}");

        return redirect()->route('admin.eskuls.show', $jadwal->eskul_id)->with('status', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(EskulJadwal $jadwal)
    {
        $eskulId = $jadwal->eskul_id;
        $jadwal->delete();

        AdminLog::record('jadwal.delete', 'eskul_jadwal', $jadwal->id, 'Menghapus jadwal eskul');

        return redirect()->route('admin.eskuls.show', $eskulId)->with('status', 'Jadwal berhasil dihapus.');
    }
}
