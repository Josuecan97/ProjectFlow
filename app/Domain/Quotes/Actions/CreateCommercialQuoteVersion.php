<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Actions;

use App\Domain\Organizations\Support\SubscriptionAccess;
use App\Domain\Quotes\Enums\QuoteStatus;
use App\Domain\Quotes\Enums\QuoteVersionStatus;
use App\Domain\Quotes\Models\Quote;
use App\Domain\Quotes\Services\QuoteDraftValidator;
use App\Domain\Quotes\Services\QuoteMemberResolver;
use App\Domain\Quotes\Services\SaveQuoteDraft;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

final class CreateCommercialQuoteVersion
{
    public function __construct(
        private readonly QuoteDraftValidator $validator,
        private readonly QuoteMemberResolver $memberResolver,
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
        $member = $this->memberResolver->resolve($actor, $quote->organization);

        if ((int) $validated['person_id'] !== $quote->person_id) {
            throw ValidationException::withMessages([
                'person_id' => __('Una nueva versión debe conservar el Cliente de la cotización original.'),
            ]);
        }

        return DB::transaction(function () use ($quote, $validated, $member): Quote {
            $lockedQuote = Quote::query()->lockForUpdate()->findOrFail($quote->id);
            $approvedVersion = $lockedQuote->approvedVersion()->first();

            if (
                $lockedQuote->status !== QuoteStatus::Approved
                || $approvedVersion === null
                || $approvedVersion->status !== QuoteVersionStatus::Approved
            ) {
                throw ValidationException::withMessages([
                    'quote' => __('Solo una cotización aprobada puede generar una revisión comercial.'),
                ]);
            }

            $versionNumber = (int) $lockedQuote->versions()->max('version_number') + 1;
            $version = $lockedQuote->versions()->create([
                'organization_id' => $lockedQuote->organization_id,
                'version_number' => $versionNumber,
                'status' => QuoteVersionStatus::Draft,
                'title' => $validated['title'],
                'issued_on' => $validated['issued_on'],
                'expires_on' => $validated['expires_on'],
                'currency' => $validated['currency'],
                'client_name' => $validated['client_name'],
                'created_by_organization_member_id' => $member->id,
            ]);

            $this->saveDraft->handle($version, $validated);
            $lockedQuote->update([
                'status' => QuoteStatus::Draft,
                'current_version_id' => $version->id,
            ]);

            return $lockedQuote->refresh()->load(['currentVersion.items', 'approvedVersion']);
        });
    }
}
