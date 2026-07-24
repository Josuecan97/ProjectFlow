<?php

namespace App\Domain\People\Enums;

enum PersonRoleCode: string
{
    case Prospect = 'prospect';
    case Client = 'client';
    case Supplier = 'supplier';
    case Partner = 'partner';
    case Contact = 'contact';
    case ExternalCollaborator = 'external_collaborator';

    public function label(): string
    {
        return match ($this) {
            self::Prospect => 'Prospecto',
            self::Client => 'Cliente',
            self::Supplier => 'Proveedor',
            self::Partner => 'Socio',
            self::Contact => 'Contacto',
            self::ExternalCollaborator => 'Colaborador externo',
        };
    }
}
