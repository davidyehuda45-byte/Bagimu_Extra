<?php

namespace App\Http\Controllers;

use App\Models\Eskul;
use App\Models\EskulJadwal;

class PublikController extends Controller
{
    public function eskul()
    {
        $eskuls = Eskul::with(['jadwals', 'pembina'])
            ->where('aktif', true)
            ->orderBy('name')
            ->get();

        return view('publik.eskul', compact('eskuls'));
    }

    public function jadwal()
    {
        $urutan = ['SENIN' => 0, 'SELASA' => 1, 'RABU' => 2, 'KAMIS' => 3, 'JUMAT' => 4, 'SABTU' => 5, 'MINGGU' => 6];

        $jadwals = EskulJadwal::with('eskul')
            ->whereHas('eskul', fn ($q) => $q->where('aktif', true))
            ->get()
            ->sortBy(fn ($j) => sprintf('%d-%s-%s', $urutan[$j->hari] ?? 9, $j->jam_mulai, $j->eskul?->name))
            ->values();

        return view('publik.jadwal', compact('jadwals'));
    }
}
