<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Actions;

use App\Domain\Organizations\Models\Organization;
use App\Domain\Organizations\Support\SubscriptionAccess;
use App\Domain\Quotes\Enums\QuoteStatus;
use App\Domain\Quotes\Enums\QuoteVersionStatus;
use App\Domain\Quotes\Models\Quote;
use App\Domain\Quotes\Services\QuoteDraftValidator;
use App\Domain\Quotes\Services\QuoteMemberResolver;
use App\Domain\Quotes\Services\QuoteNumberGenerator;
use App\Domain\Quotes\Services\SaveQuoteDraft;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final class CreateDraftQuote
{
    public function __construct(
        private readonly QuoteDraftValidator $validator,
        private readonly QuoteMemberResolver $memberResolver,
        private readonly QuoteNumberGenerator $numberGenerator,
        private readonly SaveQuoteDraft $saveDraft,
        private readonly SubscriptionAccess $subscriptionAccess,
    ) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(User $actor, Organization $organization, array $attributes): Quote
    {
        Gate::forUser($actor)->authorize('create', [Quote::class, $organization]);
        $this->subscriptionAccess->authorizeWrites($organization);

        $validated = $this->validator->validate($organization, $attributes);
        $member = $this->memberResolver->resolve($actor, $organization);

        return DB::transaction(function () use ($organization, $validated, $member): Quote {
            $quote = $organization->quotes()->create([
                'person_id' => $validated['person_id'],
                'number' => $this->numberGenerator->next($organization),
                'status' => QuoteStatus::Draft,
            ]);

            $version = $quote->versions()->create([
                'organization_id' => $organization->id,
                'version_number' => 1,
                'status' => QuoteVersionStatus::Draft,
                'title' => $validated['title'],
                'issued_on' => $validated['issued_on'],
                'expires_on' => $validated['expires_on'],
                'currency' => $validated['currency'],
                'client_name' => $validated['client_name'],
                'created_by_organization_member_id' => $member->id,
            ]);

            $this->saveDraft->handle($version, $validated);
            $quote->update(['current_version_id' => $version->id]);

            return $quote->refresh()->load(['person', 'currentVersion.items']);
        });
    }
}
