<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Actions;

use App\Domain\Organizations\Models\Organization;
use App\Domain\Quotes\Enums\QuoteStatus;
use App\Domain\Quotes\Enums\QuoteVersionStatus;
use App\Domain\Quotes\Events\QuoteExpired;
use App\Domain\Quotes\Models\Quote;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

final class ExpireQuotes
{
    public function handle(Organization $organization, ?CarbonInterface $date = null): int
    {
        $expirationDate = ($date ?? today())->toDateString();
        $expired = 0;

        Quote::query()
            ->where('organization_id', $organization->id)
            ->whereIn('status', [QuoteStatus::Draft, QuoteStatus::Sent])
            ->whereHas('currentVersion', fn ($query) => $query->whereDate('expires_on', '<', $expirationDate))
            ->select('id')
            ->chunkById(100, function ($quotes) use (&$expired, $expirationDate): void {
                foreach ($quotes as $quote) {
                    $didExpire = DB::transaction(function () use ($quote, $expirationDate): bool {
                        $lockedQuote = Quote::query()->lockForUpdate()->findOrFail($quote->id);
                        $version = $lockedQuote->currentVersion()->lockForUpdate()->first();

                        if (
                            ! in_array($lockedQuote->status, [QuoteStatus::Draft, QuoteStatus::Sent], true)
                            || $version === null
                            || ! in_array(
                                $version->status,
                                [QuoteVersionStatus::Draft, QuoteVersionStatus::Sent],
                                true,
                            )
                            || ! $version->expires_on->isBefore($expirationDate)
                        ) {
                            return false;
                        }

                        $version->update(['status' => QuoteVersionStatus::Expired]);
                        $lockedQuote->update(['status' => QuoteStatus::Expired]);
                        QuoteExpired::dispatch(
                            $lockedQuote->organization_id,
                            $lockedQuote->id,
                            $version->id,
                        );

                        return true;
                    });

                    if ($didExpire) {
                        $expired++;
                    }
                }
            });

        return $expired;
    }
}
