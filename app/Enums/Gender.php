<?php

namespace App\Enums;

enum Gender: string
{
    case LAKI_LAKI = 'LAKI_LAKI';
    case PEREMPUAN = 'PEREMPUAN';

    public function label(): string
    {
        return match ($this) {
            self::LAKI_LAKI => 'Laki-laki',
            self::PEREMPUAN => 'Perempuan',
        };
    }

    public function short(): string
    {
        return match ($this) {
            self::LAKI_LAKI => 'L',
            self::PEREMPUAN => 'P',
        };
    }
}
