<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Services;

use App\Domain\Organizations\Enums\OrganizationMemberStatus;
use App\Domain\Organizations\Models\Organization;
use App\Domain\Organizations\Models\OrganizationMember;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

final class QuoteMemberResolver
{
    public function resolve(User $actor, Organization $organization): OrganizationMember
    {
        return OrganizationMember::query()
            ->where('organization_id', $organization->id)
            ->where('user_id', $actor->id)
            ->where('status', OrganizationMemberStatus::Active)
            ->first()
            ?? throw new AuthorizationException('No existe una membresía activa para esta Organización.');
    }
}
