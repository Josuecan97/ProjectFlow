<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Services;

use App\Domain\Organizations\Models\Organization;
use App\Domain\Quotes\Models\QuoteSequence;
use DomainException;
use Illuminate\Support\Facades\DB;
use LogicException;

final class QuoteNumberGenerator
{
    public function next(Organization $organization): string
    {
        if (DB::transactionLevel() === 0) {
            throw new LogicException('Quote numbers must be generated inside a transaction.');
        }

        QuoteSequence::query()->insertOrIgnore([
            'organization_id' => $organization->id,
            'last_number' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sequence = QuoteSequence::query()
            ->whereKey($organization->id)
            ->lockForUpdate()
            ->firstOrFail();

        $next = $sequence->last_number + 1;

        if ($next > 999999) {
            throw new DomainException('The quotation number sequence has been exhausted.');
        }

        $sequence->update(['last_number' => $next]);

        return sprintf('COT-%06d', $next);
    }
}
