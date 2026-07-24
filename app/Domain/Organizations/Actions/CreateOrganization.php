<?php

namespace App\Domain\Organizations\Actions;

use App\Domain\Organizations\Enums\OrganizationMemberStatus;
use App\Domain\Organizations\Enums\SubscriptionSource;
use App\Domain\Organizations\Enums\SubscriptionStatus;
use App\Domain\Organizations\Enums\SystemRole;
use App\Domain\Organizations\Models\Organization;
use App\Domain\Organizations\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class CreateOrganization
{
    /**
     * @param  array{name: string, legal_name?: ?string, timezone?: string, locale?: string, currency?: string}  $attributes
     */
    public function handle(User $owner, array $attributes): Organization
    {
        return DB::transaction(function () use ($owner, $attributes): Organization {
            $organization = Organization::query()->create([
                'name' => $attributes['name'],
                'legal_name' => $attributes['legal_name'] ?? null,
                'timezone' => $attributes['timezone'] ?? 'America/Merida',
                'locale' => $attributes['locale'] ?? 'es_MX',
                'currency' => $attributes['currency'] ?? 'MXN',
            ]);

            $member = $organization->members()->create([
                'user_id' => $owner->id,
                'status' => OrganizationMemberStatus::Active,
                'joined_at' => now(),
            ]);

            $member->roles()->attach(
                Role::query()->where('code', SystemRole::Owner->value)->firstOrFail(),
            );

            $subscription = $organization->subscriptions()->create([
                'status' => SubscriptionStatus::Trial,
                'source' => SubscriptionSource::System,
                'starts_at' => now(),
                'ends_at' => now()->addDays(config('projectflow.trial_days')),
                'auto_renew' => false,
            ]);

            $subscription->events()->create([
                'type' => 'created',
                'actor_user_id' => $owner->id,
                'metadata' => ['trial_days' => config('projectflow.trial_days')],
                'occurred_at' => now(),
            ]);

            return $organization;
        });
    }
}
