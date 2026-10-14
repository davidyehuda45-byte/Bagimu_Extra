<?php

namespace App\Enums;

enum StudentStatus: string
{
    case AKTIF = 'AKTIF';
    case LULUS = 'LULUS';
    case PINDAH = 'PINDAH';
    case NONAKTIF = 'NONAKTIF';
    case ALUMNI = 'ALUMNI';

    public function label(): string
    {
        return match ($this) {
            self::AKTIF => 'Aktif',
            self::LULUS => 'Lulus',
            self::PINDAH => 'Pindah',
            self::NONAKTIF => 'Nonaktif',
            self::ALUMNI => 'Alumni',
        };
    }
}
