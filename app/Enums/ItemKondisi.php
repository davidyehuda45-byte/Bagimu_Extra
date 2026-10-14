<?php

namespace App\Enums;

enum ItemKondisi: string
{
    case BAIK = 'BAIK';
    case RUSAK_RINGAN = 'RUSAK_RINGAN';
    case RUSAK_BERAT = 'RUSAK_BERAT';
    case HILANG = 'HILANG';

    public function label(): string
    {
        return match ($this) {
            self::BAIK => 'Baik',
            self::RUSAK_RINGAN => 'Rusak ringan',
            self::RUSAK_BERAT => 'Rusak berat',
            self::HILANG => 'Hilang',
        };
    }
}
