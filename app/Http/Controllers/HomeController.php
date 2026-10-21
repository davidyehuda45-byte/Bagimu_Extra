<?php

namespace App\Http\Controllers;

use App\Models\Eskul;

class HomeController extends Controller
{
    public function index()
    {
        $eskuls = Eskul::with(['jadwals', 'pembina'])->where('aktif', true)->orderBy('name')->get();

        $urutanHari = ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'MINGGU'];
        $jadwal = collect($urutanHari)->mapWithKeys(function ($hari) use ($eskuls) {
            $baris = [];
            foreach ($eskuls as $eskul) {
                foreach ($eskul->jadwals->where('hari', $hari) as $j) {
                    $baris[] = ['eskul' => $eskul->name, 'jam' => $j->jam_mulai.'–'.$j->jam_selesai, 'tempat' => $j->tempat];
                }
            }
            usort($baris, fn ($a, $b) => strcmp($a['jam'], $b['jam']));

            return [$hari => $baris];
        })->filter(fn ($baris) => count($baris) > 0);

        return view('welcome', [
            'eskuls' => $eskuls,
            'jadwalMingguan' => $jadwal,
        ]);
    }
}
