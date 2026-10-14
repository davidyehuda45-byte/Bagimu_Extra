<?php

namespace App\Support;

use App\Enums\Status;
use App\Models\Eskul;
use App\Models\Registration;

class Kuota
{
    /**
     * Keterisian eskul = pendaftar non-REJECTED pada periode.
     */
    public static function terisi(Eskul $eskul, string $periode, ?int $kecualiId = null): int
    {
        return Registration::where('eskul_utama', $eskul->name)
            ->where('periode', $periode)
            ->where('status', '!=', Status::REJECTED->value)
            ->when($kecualiId, fn ($q) => $q->where('id', '!=', $kecualiId))
            ->count();
    }

    /**
     * Penuh bila kuota ditegakkan (kuota_aktif) dan terisi >= kuota.
     * Tanpa kuotaAktif = bebas tanpa batas.
     */
    public static function penuh(Eskul $eskul, string $periode, ?int $kecualiId = null): bool
    {
        return $eskul->kuota_aktif && self::terisi($eskul, $periode, $kecualiId) >= $eskul->kuota;
    }
}
