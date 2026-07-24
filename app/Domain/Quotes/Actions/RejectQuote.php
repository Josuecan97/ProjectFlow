<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Actions;

use App\Domain\Organizations\Support\SubscriptionAccess;
use App\Domain\Quotes\Enums\QuoteStatus;
use App\Domain\Quotes\Enums\QuoteVersionStatus;
use App\Domain\Quotes\Models\Quote;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

final class RejectQuote
{
    public function __construct(private readonly SubscriptionAccess $subscriptionAccess) {}

    public function handle(User $actor, Quote $quote): Quote
    {
        Gate::forUser($actor)->authorize('approve', $quote);
        $this->subscriptionAccess->authorizeWrites($quote->organization);

        return DB::transaction(function () use ($quote): Quote {
            $lockedQuote = Quote::query()->lockForUpdate()->findOrFail($quote->id);
            $version = $lockedQuote->currentVersion()->lockForUpdate()->first();

            if (
                ! $lockedQuote->status->canTransitionTo(QuoteStatus::Rejected)
                || $version === null
                || $version->status !== QuoteVersionStatus::Sent
            ) {
                throw ValidationException::withMessages([
                    'quote' => __('La cotización no puede rechazarse desde su estado actual.'),
                ]);
            }

            $version->update(['status' => QuoteVersionStatus::Rejected]);
            $lockedQuote->update(['status' => QuoteStatus::Rejected]);

            return $lockedQuote->refresh()->load('currentVersion');
        });
    }
}
