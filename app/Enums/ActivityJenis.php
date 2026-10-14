<?php

namespace App\Enums;

enum ActivityJenis: string
{
    case LATIHAN = 'LATIHAN';
    case LOMBA = 'LOMBA';
    case ACARA = 'ACARA';

    public function label(): string
    {
        return match ($this) {
            self::LATIHAN => 'Latihan',
            self::LOMBA => 'Lomba',
            self::ACARA => 'Acara',
        };
    }
}
