<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case TEACHER = 'teacher';
    case USER = 'user';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::TEACHER => 'Prowadzący',
            self::USER => 'Opiekun',
        };
    }
}


