<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PeriodeStatus;
use App\Enums\Role;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Eskul;
use App\Models\NilaiHistory;
use App\Models\Periode;
use App\Models\Registration;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    public function index(Request $request)
    {
        $eskuls = Eskul::orderBy('name')->get();

        if (auth()->user()->hasRole(Role::PEMBINA)) {
            $eskuls = $this->eskulPembina(auth()->id());
        }

        $eskulId = $request->query('eskul_id', $eskuls->first()?->id);
        $eskul = $eskuls->firstWhere('id', (int) $eskulId) ?? $eskuls->first();

        $periodeAktif = Periode::where('status', PeriodeStatus::DIBUKA)->orderByDesc('periode')->first();

        $pendaftar = collect();

        if ($eskul) {
            $q = Registration::with('user')
                ->where('status', Status::APPROVED)
                ->where('eskul_utama', $eskul->name)
                ->orderBy('kelas')->orderBy('nis');

            if ($periodeAktif) {
                $q->where('periode', $periodeAktif->periode);
            }

            $pendaftar = $q->get();
        }

        return view('admin.nilai.index', [
            'eskuls' => $eskuls,
            'eskul' => $eskul,
            'pendaftar' => $pendaftar,
            'periodeAktif' => $periodeAktif,
        ]);
    }

    public function store(Request $request)
    {
        // Predikat kosong dari form ("Otomatis") → null agar dihitung otomatis.
        $predikatInput = $request->input('predikat', []);
        if (is_array($predikatInput)) {
            foreach ($predikatInput as $k => $v) {
                if ($v === '') {
                    $request->merge(["predikat.{$k}" => null]);
                }
            }
        }

        $data = $request->validate([
            'eskul_id' => ['required', 'exists:eskuls,id'],
            'nilai' => ['required', 'array'],
            'nilai.*' => ['nullable', 'integer', 'min:0', 'max:100'],
            'predikat' => ['nullable', 'array'],
            'predikat.*' => ['nullable', 'string', 'in:A,B,C,D'],
            'catatan' => ['nullable', 'array'],
            'catatan.*' => ['nullable', 'string', 'max:1000'],
        ]);

        $eskul = Eskul::findOrFail($data['eskul_id']);

        if (auth()->user()->hasRole(Role::PEMBINA)) {
            $boleh = $this->eskulPembina(auth()->id())->contains('id', $eskul->id);
            if (! $boleh) {
                abort(403, 'Eskul wajib milik Anda.');
            }
        }

        $disimpan = 0;

        foreach ($data['nilai'] as $registrationId => $nilai) {
            if ($nilai === null) {
                continue;
            }

            $reg = Registration::find($registrationId);
            if (! $reg) {
                continue;
            }

            if ($reg->eskul_utama !== $eskul->name) {
                continue;
            }

            $predikat = $data['predikat'][$registrationId] ?? null;
            $catatan = $data['catatan'][$registrationId] ?? null;

            if (($predikat === null || $predikat === '') && $nilai !== null) {
                $predikat = $this->predikatOtomatis((int) $nilai);
            }
            $predikat = $predikat ?: $this->predikatOtomatis((int) $nilai);

            $reg->update([
                'nilai' => $nilai,
                'predikat' => $predikat ?: null,
                'catatan_nilai' => $catatan,
            ]);

            NilaiHistory::create([
                'registration_id' => $reg->id,
                'nilai' => $nilai,
                'predikat' => $predikat ?: null,
                'catatan' => $catatan,
                'actor_id' => (string) auth()->id(),
                'actor_email' => auth()->user()->email,
            ]);

            $disimpan++;
        }

        AdminLog::record('nilai.store', 'Eskul', (string) $eskul->id, "Simpan nilai {$eskul->name} ({$disimpan} baris).");

        return back()->with('status', "Nilai tersimpan ({$disimpan} siswa).");
    }

    private function predikatOtomatis(int $nilai): string
    {
        return match (true) {
            $nilai >= 85 => 'A',
            $nilai >= 70 => 'B',
            $nilai >= 55 => 'C',
            default => 'D',
        };
    }

    private function eskulPembina(int $userId)
    {
        return Eskul::where('pembina_id', $userId)
            ->orWhere(function ($q) use ($userId) {
                $q->where('temp_pembina_id', $userId)
                    ->whereDate('temp_pembina_sampai', '>=', today());
            })
            ->orderBy('name')->get();
    }
}
