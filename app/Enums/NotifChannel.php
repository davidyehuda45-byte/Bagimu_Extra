<?php

namespace App\Enums;

enum NotifChannel: string
{
    case EMAIL = 'EMAIL';
    case WA = 'WA';
    case INAPP = 'INAPP';

    public function label(): string
    {
        return match ($this) {
            self::EMAIL => 'Email',
            self::WA => 'WhatsApp',
            self::INAPP => 'Dalam aplikasi',
        };
    }
}
