<?php

namespace App\Enums;

enum LoginMethod: string
{
    case GOOGLE = 'GOOGLE';
    case USERNAME = 'USERNAME';
    case TOKEN = 'TOKEN';
    case PASSWORD = 'PASSWORD';

    public function label(): string
    {
        return match ($this) {
            self::GOOGLE => 'Google',
            self::USERNAME => 'Username',
            self::TOKEN => 'Token',
            self::PASSWORD => 'Password',
        };
    }
}
