<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Actions;

use App\Domain\Organizations\Support\SubscriptionAccess;
use App\Domain\Quotes\Enums\QuoteStatus;
use App\Domain\Quotes\Enums\QuoteVersionStatus;
use App\Domain\Quotes\Models\Quote;
use App\Domain\Quotes\Services\QuoteDraftValidator;
use App\Domain\Quotes\Services\SaveQuoteDraft;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

final class UpdateDraftQuote
{
    public function __construct(
        private readonly QuoteDraftValidator $validator,
        private readonly SaveQuoteDraft $saveDraft,
        private readonly SubscriptionAccess $subscriptionAccess,
    ) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(User $actor, Quote $quote, array $attributes): Quote
    {
        Gate::forUser($actor)->authorize('update', $quote);
        $this->subscriptionAccess->authorizeWrites($quote->organization);

        $validated = $this->validator->validate($quote->organization, $attributes);

        return DB::transaction(function () use ($quote, $validated): Quote {
            $lockedQuote = Quote::query()->lockForUpdate()->findOrFail($quote->id);
            $version = $lockedQuote->currentVersion()->lockForUpdate()->first();

            if (
                $lockedQuote->status !== QuoteStatus::Draft
                || $version === null
                || $version->status !== QuoteVersionStatus::Draft
            ) {
                throw ValidationException::withMessages([
                    'quote' => __('Solo una cotización Draft puede editarse directamente.'),
                ]);
            }

            $lockedQuote->update(['person_id' => $validated['person_id']]);
            $this->saveDraft->handle($version, $validated);

            return $lockedQuote->refresh()->load(['person', 'currentVersion.items']);
        });
    }
}
