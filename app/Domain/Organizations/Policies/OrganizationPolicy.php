<?php

namespace App\Domain\Organizations\Policies;

use App\Domain\Organizations\Enums\OrganizationMemberStatus;
use App\Domain\Organizations\Models\Organization;
use App\Models\User;

final class OrganizationPolicy
{
    public function update(User $user, Organization $organization): bool
    {
        return $user->organizationMembers()
            ->where('organization_id', $organization->id)
            ->where('status', OrganizationMemberStatus::Active)
            ->whereHas('roles.permissions', fn ($query) => $query->where('code', 'organization.manage'))
            ->exists();
    }

    public function viewMembers(User $user, Organization $organization): bool
    {
        return $this->hasPermission($user, $organization, 'members.view');
    }

    public function manageMembers(User $user, Organization $organization): bool
    {
        return $this->hasPermission($user, $organization, 'members.update');
    }

    public function inviteMembers(User $user, Organization $organization): bool
    {
        return $this->hasPermission($user, $organization, 'members.invite');
    }

    public function removeMembers(User $user, Organization $organization): bool
    {
        return $this->hasPermission($user, $organization, 'members.remove');
    }

    public function transfer(User $user, Organization $organization): bool
    {
        return $this->hasPermission($user, $organization, 'organization.transfer');
    }

    public function viewRoles(User $user, Organization $organization): bool
    {
        return $this->hasPermission($user, $organization, 'roles.view');
    }

    public function viewSubscription(User $user, Organization $organization): bool
    {
        return $this->hasPermission($user, $organization, 'subscription.view');
    }

    private function hasPermission(User $user, Organization $organization, string $permission): bool
    {
        return $user->organizationMembers()
            ->where('organization_id', $organization->id)
            ->where('status', OrganizationMemberStatus::Active)
            ->whereHas('roles.permissions', fn ($query) => $query->where('code', $permission))
            ->exists();
    }
}
