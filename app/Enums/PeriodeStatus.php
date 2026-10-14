<?php

namespace App\Enums;

enum PeriodeStatus: string
{
    case DRAFT = 'DRAFT';
    case TERJADWAL = 'TERJADWAL';
    case DIBUKA = 'DIBUKA';
    case DITUTUP = 'DITUTUP';
    case DIARSIPKAN = 'DIARSIPKAN';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draf',
            self::TERJADWAL => 'Terjadwal',
            self::DIBUKA => 'Dibuka',
            self::DITUTUP => 'Ditutup',
            self::DIARSIPKAN => 'Diarsipkan',
        };
    }
}
