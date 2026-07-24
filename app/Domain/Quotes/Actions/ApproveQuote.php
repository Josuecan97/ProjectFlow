<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Actions;

use App\Domain\Organizations\Support\SubscriptionAccess;
use App\Domain\Quotes\Enums\QuoteStatus;
use App\Domain\Quotes\Enums\QuoteVersionStatus;
use App\Domain\Quotes\Events\QuoteApproved;
use App\Domain\Quotes\Models\Quote;
use App\Domain\Quotes\Services\QuoteMemberResolver;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

final class ApproveQuote
{
    public function __construct(
        private readonly QuoteMemberResolver $memberResolver,
        private readonly SubscriptionAccess $subscriptionAccess,
    ) {}

    public function handle(User $actor, Quote $quote): Quote
    {
        Gate::forUser($actor)->authorize('approve', $quote);
        $this->subscriptionAccess->authorizeWrites($quote->organization);
        $member = $this->memberResolver->resolve($actor, $quote->organization);

        return DB::transaction(function () use ($quote, $member): Quote {
            $lockedQuote = Quote::query()->lockForUpdate()->findOrFail($quote->id);
            $version = $lockedQuote->currentVersion()->lockForUpdate()->first();

            if (
                ! $lockedQuote->status->canTransitionTo(QuoteStatus::Approved)
                || $version === null
                || $version->status !== QuoteVersionStatus::Sent
            ) {
                throw ValidationException::withMessages([
                    'quote' => __('La cotización debe estar enviada antes de aprobarse.'),
                ]);
            }

            if ($version->expires_on->isBefore(today())) {
                throw ValidationException::withMessages([
                    'expires_on' => __('Una cotización vencida no puede aprobarse.'),
                ]);
            }

            $approvedAt = now();
            $version->update([
                'status' => QuoteVersionStatus::Approved,
                'approved_at' => $approvedAt,
            ]);
            $lockedQuote->update([
                'status' => QuoteStatus::Approved,
                'approved_version_id' => $version->id,
                'approved_at' => $approvedAt,
                'approved_by_organization_member_id' => $member->id,
            ]);

            QuoteApproved::dispatch(
                $lockedQuote->organization_id,
                $lockedQuote->id,
                $version->id,
                $member->id,
            );

            return $lockedQuote->refresh()->load(['currentVersion', 'approvedVersion']);
        });
    }
}
