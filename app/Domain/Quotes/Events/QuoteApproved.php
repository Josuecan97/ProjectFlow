<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

final class QuoteApproved implements ShouldDispatchAfterCommit
{
    use Dispatchable;

    public function __construct(
        public readonly int $organizationId,
        public readonly int $quoteId,
        public readonly int $quoteVersionId,
        public readonly int $approvedByOrganizationMemberId,
    ) {}
}
