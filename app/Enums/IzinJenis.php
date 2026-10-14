<?php

namespace App\Enums;

enum IzinJenis: string
{
    case IZIN = 'IZIN';
    case SAKIT = 'SAKIT';
    case DISPENSASI = 'DISPENSASI';

    public function label(): string
    {
        return match ($this) {
            self::IZIN => 'Izin',
            self::SAKIT => 'Sakit',
            self::DISPENSASI => 'Dispensasi',
        };
    }
}
