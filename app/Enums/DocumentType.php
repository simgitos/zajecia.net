<?php

declare(strict_types=1);

namespace App\Enums;

enum DocumentType: string
{
    case TERMS = 'terms';
    case RODO = 'rodo';
    case CHILD_PROTECTION = 'child_protection';
    case PRIVACY_POLICY = 'privacy_policy';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::TERMS => 'Regulamin placówki',
            self::RODO => 'Klauzula informacyjna RODO',
            self::CHILD_PROTECTION => 'Standardy ochrony małoletnich',
            self::PRIVACY_POLICY => 'Polityka prywatności',
            self::OTHER => 'Inny dokument',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::TERMS => 'ti-file-certificate',
            self::RODO => 'ti-shield-check',
            self::CHILD_PROTECTION => 'ti-heart-handshake',
            self::PRIVACY_POLICY => 'ti-shield-lock',
            self::OTHER => 'ti-file-text',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::TERMS => 'primary',
            self::RODO => 'azure',
            self::CHILD_PROTECTION => 'danger',
            self::PRIVACY_POLICY => 'info',
            self::OTHER => 'secondary',
        };
    }
}
