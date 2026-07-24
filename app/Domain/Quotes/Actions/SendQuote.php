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

final class SendQuote
{
    public function __construct(private readonly SubscriptionAccess $subscriptionAccess) {}

    public function handle(User $actor, Quote $quote): Quote
    {
        Gate::forUser($actor)->authorize('update', $quote);
        $this->subscriptionAccess->authorizeWrites($quote->organization);

        return DB::transaction(function () use ($quote): Quote {
            $lockedQuote = Quote::query()->lockForUpdate()->findOrFail($quote->id);
            $version = $lockedQuote->currentVersion()->lockForUpdate()->first();

            if (
                ! $lockedQuote->status->canTransitionTo(QuoteStatus::Sent)
                || $version === null
                || $version->status !== QuoteVersionStatus::Draft
            ) {
                throw ValidationException::withMessages([
                    'quote' => __('La cotización no puede enviarse desde su estado actual.'),
                ]);
            }

            if ($version->expires_on->isBefore(today())) {
                throw ValidationException::withMessages([
                    'expires_on' => __('La cotización vencida no puede enviarse.'),
                ]);
            }

            $version->update(['status' => QuoteVersionStatus::Sent]);
            $lockedQuote->update(['status' => QuoteStatus::Sent]);

            return $lockedQuote->refresh()->load('currentVersion');
        });
    }
}
