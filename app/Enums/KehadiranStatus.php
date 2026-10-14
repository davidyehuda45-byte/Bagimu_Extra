<?php

namespace App\Enums;

enum KehadiranStatus: string
{
    case HADIR = 'HADIR';
    case IZIN = 'IZIN';
    case SAKIT = 'SAKIT';
    case ALPA = 'ALPA';

    public function label(): string
    {
        return match ($this) {
            self::HADIR => 'Hadir',
            self::IZIN => 'Izin',
            self::SAKIT => 'Sakit',
            self::ALPA => 'Alpa',
        };
    }
}
