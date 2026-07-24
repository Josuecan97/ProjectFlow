<?php

declare(strict_types=1);

namespace App\Domain\Quotes\ValueObjects;

final readonly class GeneratedQuotePdf
{
    public function __construct(
        public string $filename,
        public string $content,
    ) {}
}
