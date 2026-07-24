<?php

namespace App\Domain\People\Enums;

enum PersonType: string
{
    case Individual = 'individual';
    case Organization = 'organization';

    public function label(): string
    {
        return match ($this) {
            self::Individual => 'Persona física',
            self::Organization => 'Persona moral',
        };
    }
}
