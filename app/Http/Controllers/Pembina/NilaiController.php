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

            $predikat = match (true) {
                $nilai >= 85 => 'A',
                $nilai >= 70 => 'B',
                $nilai >= 55 => 'C',
                default => 'D',
            };

            DB::transaction(function () use ($reg, $nilai, $predikat, $data, $registrationId, $me) {
                $reg->update([
                    'nilai' => $nilai,
                    'predikat' => $predikat,
                    'catatan_nilai' => $data['catatan'][$registrationId] ?? null,
                ]);

                NilaiHistory::create([
                    'registration_id' => $reg->id,
                    'nilai' => $nilai,
                    'predikat' => $predikat,
                    'catatan' => $data['catatan'][$registrationId] ?? null,
                    'actor_id' => $me->id,
                    'actor_email' => $me->email,
                ]);
            });

            $ok++;
        }

        AdminLog::record('pembina.nilai.store', 'Eskul', (string) $eskul->id, "Simpan {$ok} nilai eskul {$eskul->name}.");

        return back()->with('status', $ok > 0 ? "{$ok} nilai tersimpan." : 'Tidak ada nilai valid untuk disimpan.');
    }
}
