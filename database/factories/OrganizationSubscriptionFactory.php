<?php

namespace Database\Factories;

use App\Domain\Organizations\Enums\SubscriptionSource;
use App\Domain\Organizations\Enums\SubscriptionStatus;
use App\Domain\Organizations\Models\Organization;
use App\Domain\Organizations\Models\OrganizationSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrganizationSubscription>
 */
final class OrganizationSubscriptionFactory extends Factory
{
    protected $model = OrganizationSubscription::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'status' => SubscriptionStatus::Trial,
            'source' => SubscriptionSource::System,
            'starts_at' => now(),
            'ends_at' => now()->addDays(14),
            'auto_renew' => false,
        ];
    }
}
