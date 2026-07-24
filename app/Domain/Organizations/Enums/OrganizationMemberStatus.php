<?php

namespace App\Domain\Organizations\Enums;

enum OrganizationMemberStatus: string
{
    case Invited = 'invited';
    case Active = 'active';
    case Suspended = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::Invited => 'Invitado',
            self::Active => 'Activo',
            self::Suspended => 'Suspendido',
        };
    }
}
