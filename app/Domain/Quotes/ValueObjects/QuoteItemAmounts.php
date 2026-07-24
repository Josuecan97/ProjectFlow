<?php

declare(strict_types=1);

namespace App\Domain\Quotes\ValueObjects;

final readonly class QuoteItemAmounts
{
    public function __construct(
        public string $subtotal,
        public string $taxAmount,
        public string $total,
    ) {}
}
