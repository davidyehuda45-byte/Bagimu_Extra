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
}
