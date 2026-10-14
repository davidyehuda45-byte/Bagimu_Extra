<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Jurusan;
use App\Http\Controllers\Controller;
use App\Models\Periode;
use App\Models\Registration;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $periodes = Periode::orderByDesc('periode')->get();
        $aktif = Periode::where('status', \App\Enums\PeriodeStatus::DIBUKA)->orderByDesc('periode')->first();
        $periode = $request->query('periode', $aktif?->periode ?? $periodes->first()?->periode);

        if ($periode && ! preg_match('/^\d{4}\/\d{4}$/', $periode)) {
            $periode = $aktif?->periode ?? $periodes->first()?->periode;
        }

        $regs = Registration::with('user')
            ->when($periode, fn ($q) => $q->where('periode', $periode))
            ->get();

        $jurusans = Jurusan::cases();

        $kelasList = $regs->pluck('kelas')->filter()->unique()
            ->sortBy(fn ($k) => $this->urutKelas($k))->values();

        $matrix = [];
        foreach ($kelasList as $k) {
            foreach ($jurusans as $j) {
                $matrix[$k][$j->value] = $regs
                    ->filter(fn ($r) => $r->kelas === $k && $r->jurusan?->value === $j->value)
                    ->count();
            }
        }

        $rincian = collect($jurusans)->mapWithKeys(function ($j) use ($regs) {
            $milik = $regs->filter(fn ($r) => $r->jurusan?->value === $j->value);

            return [$j->value => [
                'jurusan' => $j,
                'total' => $milik->count(),
                'perKelas' => $milik->groupBy('kelas')->sortKeysUsing(fn ($a, $b) => strcmp($this->urutKelas($a), $this->urutKelas($b))),
            ]];
        });

        return view('admin.kelas.index', compact('periodes', 'periode', 'jurusans', 'kelasList', 'matrix', 'rincian'));
    }

    private function urutKelas(string $k): string
    {
        if (preg_match('/^(\d+)\s*(.*)$/', $k, $m)) {
            return sprintf('%02d-%s', (int) $m[1], $m[2]);
        }

        return '99-'.$k;
    }
}
