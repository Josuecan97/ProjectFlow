<?php

namespace App\Domain\People\Policies;

use App\Domain\Organizations\Enums\OrganizationMemberStatus;
use App\Domain\Organizations\Models\Organization;
use App\Domain\Organizations\Support\CurrentOrganization;
use App\Domain\People\Models\Person;
use App\Models\User;

final class PersonPolicy
{
    public function __construct(private readonly CurrentOrganization $currentOrganization) {}

    public function viewAny(User $user, Organization $organization): bool
    {
        return $this->hasPermission($user, $organization->id, 'people.view');
    }

    public function view(User $user, Person $person): bool
    {
        return $this->hasPermission($user, $person->organization_id, 'people.view');
    }

    public function create(User $user, Organization $organization): bool
    {
        return $this->hasPermission($user, $organization->id, 'people.create');
    }

    public function update(User $user, Person $person): bool
    {
        return $this->hasPermission($user, $person->organization_id, 'people.update');
    }

    public function updateAny(User $user, Organization $organization): bool
    {
        return $this->hasPermission($user, $organization->id, 'people.update');
    }

    public function archive(User $user, Person $person): bool
    {
        return $this->hasPermission($user, $person->organization_id, 'people.archive');
    }

    public function archiveAny(User $user, Organization $organization): bool
    {
        return $this->hasPermission($user, $organization->id, 'people.archive');
    }

    public function restore(User $user, Person $person): bool
    {
        return $this->archive($user, $person);
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
