<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Enums;

enum QuoteVersionRevisionType: string
{
    case AdministrativeCorrection = 'administrative_correction';

    public function label(): string
    {
        return 'Corrección administrativa';
    }
}
