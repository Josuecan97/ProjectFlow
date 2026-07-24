<?php

namespace App\Domain\Organizations\Support;

use App\Domain\Organizations\Models\Organization;
use LogicException;

final class CurrentOrganization
{
    private ?Organization $organization = null;

    public function set(Organization $organization): void
    {
        $this->organization = $organization;
    }

    public function get(): Organization
    {
        return $this->organization
            ?? throw new LogicException('No organization has been resolved for the current request.');
    }

    public function has(): bool
    {
        return $this->organization !== null;
    }

    public function id(): int
    {
        return $this->get()->getKey();
    }
}
