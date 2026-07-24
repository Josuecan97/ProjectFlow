<?php

namespace App\Domain\Organizations\Support;

use App\Domain\Organizations\Enums\SubscriptionStatus;
use App\Domain\Organizations\Models\Organization;
use App\Domain\Organizations\Models\OrganizationSubscription;
use Illuminate\Auth\Access\AuthorizationException;

final class SubscriptionAccess
{
    public function current(Organization $organization): ?OrganizationSubscription
    {
        $subscription = $organization->currentSubscription;

        if (
            $subscription !== null
            && in_array($subscription->status, [SubscriptionStatus::Trial, SubscriptionStatus::Active], true)
            && $subscription->ends_at?->isPast()
        ) {
            $subscription->update(['status' => SubscriptionStatus::Expired]);
            $subscription->events()->create([
                'type' => 'expired',
                'metadata' => ['previous_status' => $subscription->getOriginal('status')],
                'occurred_at' => now(),
            ]);
        }

        return $subscription;
    }

    public function allowsWrites(Organization $organization): bool
    {
        return $this->current($organization)?->allowsWrites() ?? false;
    }

    public function authorizeWrites(Organization $organization): void
    {
        if (! $this->allowsWrites($organization)) {
            throw new AuthorizationException(
                'La membresía no está vigente. La organización se encuentra en modo de solo lectura.',
            );
        }
    }
}
