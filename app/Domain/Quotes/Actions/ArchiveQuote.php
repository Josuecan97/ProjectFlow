<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Actions;

use App\Domain\Organizations\Support\SubscriptionAccess;
use App\Domain\Quotes\Enums\QuoteStatus;
use App\Domain\Quotes\Models\Quote;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

final class ArchiveQuote
{
    public function __construct(private readonly SubscriptionAccess $subscriptionAccess) {}

    public function handle(User $actor, Quote $quote): Quote
    {
        Gate::forUser($actor)->authorize('archive', $quote);
        $this->subscriptionAccess->authorizeWrites($quote->organization);

        return DB::transaction(function () use ($quote): Quote {
            $lockedQuote = Quote::query()->lockForUpdate()->findOrFail($quote->id);

            if (! $lockedQuote->status->canTransitionTo(QuoteStatus::Archived)) {
                throw ValidationException::withMessages([
                    'quote' => __('La cotización no puede archivarse desde su estado actual.'),
                ]);
            }

            $lockedQuote->update(['status' => QuoteStatus::Archived]);

            return $lockedQuote->refresh();
        });
    }
}
