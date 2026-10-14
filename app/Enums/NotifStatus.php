<?php

namespace App\Enums;

enum NotifStatus: string
{
    case MENUNGGU = 'MENUNGGU';
    case TERKIRIM = 'TERKIRIM';
    case GAGAL = 'GAGAL';

    public function label(): string
    {
        return match ($this) {
            self::MENUNGGU => 'Menunggu',
            self::TERKIRIM => 'Terkirim',
            self::GAGAL => 'Gagal',
        };
    }
}
