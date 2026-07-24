<?php

namespace App\Domain\People\Enums;

enum PersonStatus: string
{
    case Active = 'active';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Activa',
            self::Archived => 'Archivada',
        };
    }
}
