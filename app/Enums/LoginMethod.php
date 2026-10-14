<?php

namespace App\Enums;

enum LoginMethod: string
{
    case GOOGLE = 'GOOGLE';
    case USERNAME = 'USERNAME';
    case TOKEN = 'TOKEN';
}
