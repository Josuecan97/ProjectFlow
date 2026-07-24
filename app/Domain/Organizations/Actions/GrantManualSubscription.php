<?php

namespace App\Domain\Organizations\Actions;

use App\Domain\Organizations\Enums\SubscriptionSource;
use App\Domain\Organizations\Enums\SubscriptionStatus;
use App\Domain\Organizations\Models\Organization;
use App\Domain\Organizations\Models\OrganizationSubscription;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

final class GrantManualSubscription
{
    public function handle(
        Organization $organization,
        User $actor,
        CarbonInterface $startsAt,
        CarbonInterface $endsAt,
        ?string $notes = null,
    ): OrganizationSubscription {
        return DB::transaction(function () use ($organization, $actor, $startsAt, $endsAt, $notes) {
            $organization->subscriptions()
                ->whereIn('status', [SubscriptionStatus::Trial, SubscriptionStatus::Active])
                ->update(['status' => SubscriptionStatus::Cancelled]);

            $subscription = $organization->subscriptions()->create([
                'status' => SubscriptionStatus::Active,
                'source' => SubscriptionSource::Manual,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'auto_renew' => false,
                'granted_by_user_id' => $actor->id,
                'notes' => $notes,
            ]);

            $subscription->events()->create([
                'type' => 'manual_grant',
                'actor_user_id' => $actor->id,
                'metadata' => ['notes' => $notes],
                'occurred_at' => now(),
            ]);

            return $subscription;
        });
    }
}
