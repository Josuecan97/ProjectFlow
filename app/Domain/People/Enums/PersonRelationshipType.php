<?php

namespace App\Domain\People\Enums;

enum PersonRelationshipType: string
{
    case Contact = 'contact';

    public function label(): string
    {
        return 'Contacto';
    }
}
