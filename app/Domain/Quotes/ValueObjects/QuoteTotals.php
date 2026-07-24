<?php

declare(strict_types=1);

namespace App\Domain\Quotes\ValueObjects;

final readonly class QuoteTotals
{
    public function __construct(
        public string $subtotal,
        public string $discountTotal,
        public string $taxTotal,
        public string $total,
    ) {}
}
