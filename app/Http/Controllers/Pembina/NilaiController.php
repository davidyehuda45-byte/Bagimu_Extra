<?php

namespace App\Http\Controllers\Pembina;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Eskul;
use App\Models\NilaiHistory;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NilaiController extends Controller
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
        $me = $request->user();

        $this->pastikanMilik($eskul, $me->id);

        // Simpan satu siswa (form per baris).
        if ($request->has('registration_id')) {
            $satu = $request->validate([
                'registration_id' => ['required', 'integer', 'exists:registrations,id'],
                'nilai' => ['required', 'integer', 'min:0', 'max:100'],
                'catatan' => ['nullable', 'string', 'max:1000'],
            ]);

            $reg = Registration::where('id', $satu['registration_id'])
                ->where('status', Status::APPROVED)
                ->first();

            if (! $reg || $reg->eskul_utama !== $eskul->name) {
                return back()->withErrors(['nilai' => 'Data siswa tidak valid untuk eskul ini.']);
            }

            $this->simpan($reg, (int) $satu['nilai'], $satu['catatan'] ?? null, $me);

            AdminLog::record('pembina.nilai.store', 'Eskul', (string) $eskul->id, "Simpan nilai {$reg->user?->name} ({$eskul->name}).");

            return back()->with('status', "Nilai {$reg->user?->name} tersimpan.");
        }

        $data = $request->validate([
            'nilai' => ['required', 'array'],
            'nilai.*' => ['nullable', 'integer', 'min:0', 'max:100'],
            'catatan' => ['nullable', 'array'],
            'catatan.*' => ['nullable', 'string', 'max:1000'],
        ]);

        $ok = 0;

        foreach ($data['nilai'] as $registrationId => $nilai) {
            if ($nilai === null) {
                continue;
            }

            $reg = Registration::where('id', $registrationId)
                ->where('status', Status::APPROVED)
                ->first();

            if (! $reg) {
                continue;
            }

            if ($reg->eskul_utama !== $eskul->name) {
                continue;
            }

            $this->simpan($reg, (int) $nilai, $data['catatan'][$registrationId] ?? null, $me);

            $ok++;
        }

        AdminLog::record('pembina.nilai.store', 'Eskul', (string) $eskul->id, "Simpan {$ok} nilai eskul {$eskul->name}.");

        return back()->with('status', $ok > 0 ? "{$ok} nilai tersimpan." : 'Tidak ada nilai valid untuk disimpan.');
    }

    private function simpan(Registration $reg, int $nilai, ?string $catatan, $me): void
    {
        $predikat = match (true) {
            $nilai >= 85 => 'A',
            $nilai >= 70 => 'B',
            $nilai >= 55 => 'C',
            default => 'D',
        };

        DB::transaction(function () use ($reg, $nilai, $predikat, $catatan, $me) {
            $reg->update([
                'nilai' => $nilai,
                'predikat' => $predikat,
                'catatan_nilai' => $catatan,
            ]);

            NilaiHistory::create([
                'registration_id' => $reg->id,
                'nilai' => $nilai,
                'predikat' => $predikat,
                'catatan' => $catatan,
                'actor_id' => $me->id,
                'actor_email' => $me->email,
            ]);
        });
    }
}
