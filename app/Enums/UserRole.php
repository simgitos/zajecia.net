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

    public function icon(): string
    {
        return match($this) {
            self::ADMIN => 'ti-shield-lock',
            self::TEACHER => 'ti-chalkboard',
            self::USER => 'ti-user',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ADMIN => 'danger',
            self::TEACHER => 'warning',
            self::USER => 'primary',
        };
    }

    /**
     * Priorytet roli (wyższy = ważniejsza). Używany do ustalania domyślnej aktywnej roli.
     */
    public function priority(): int
    {
        return match($this) {
            self::ADMIN => 3,
            self::TEACHER => 2,
            self::USER => 1,
        };
    }
}
