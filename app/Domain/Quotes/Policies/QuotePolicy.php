<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Policies;

use App\Domain\Organizations\Enums\OrganizationMemberStatus;
use App\Domain\Organizations\Models\Organization;
use App\Domain\Organizations\Support\CurrentOrganization;
use App\Domain\Quotes\Models\Quote;
use App\Models\User;

final class QuotePolicy
{
    public function __construct(private readonly CurrentOrganization $currentOrganization) {}

    public function viewAny(User $user, Organization $organization): bool
    {
        return $this->hasPermission($user, $organization->id, 'quotes.view');
    }

    public function view(User $user, Quote $quote): bool
    {
        return $this->hasPermission($user, $quote->organization_id, 'quotes.view');
    }

    public function create(User $user, Organization $organization): bool
    {
        return $this->hasPermission($user, $organization->id, 'quotes.create');
    }

    public function update(User $user, Quote $quote): bool
    {
        return $this->hasPermission($user, $quote->organization_id, 'quotes.update');
    }

    public function approve(User $user, Quote $quote): bool
    {
        return $this->hasPermission($user, $quote->organization_id, 'quotes.approve');
    }

    public function archive(User $user, Quote $quote): bool
    {
        return $this->hasPermission($user, $quote->organization_id, 'quotes.archive');
    }

    private function hasPermission(User $user, int $organizationId, string $permission): bool
    {
        if (
            $this->currentOrganization->has()
            && $this->currentOrganization->id() !== $organizationId
        ) {
            return false;
        }

        return $user->organizationMembers()
            ->where('organization_id', $organizationId)
            ->where('status', OrganizationMemberStatus::Active)
            ->whereHas('roles.permissions', fn ($query) => $query->where('code', $permission))
            ->exists();
    }
}
