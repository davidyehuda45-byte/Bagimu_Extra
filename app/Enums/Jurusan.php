<?php

namespace App\Enums;

enum Jurusan: string
{
    case RPL = 'RPL';
    case KULINER = 'KULINER';
    case DKV = 'DKV';
    case TP = 'TP';
    case TKP = 'TKP';

    public function label(): string
    {
        return match ($this) {
            self::RPL => 'RPL (Rekayasa Perangkat Lunak)',
            self::KULINER => 'Kuliner',
            self::DKV => 'DKV (Desain Komunikasi Visual)',
            self::TP => 'TP (Teknik Pengelasan)',
            self::TKP => 'TKP (Teknik Konstruksi Perumahan)',
        };
    }

    public function short(): string
    {
        return match ($this) {
            self::RPL => 'RPL',
            self::KULINER => 'Kuliner',
            self::DKV => 'DKV',
            self::TP => 'TP',
            self::TKP => 'TKP',
        };
    }
}
