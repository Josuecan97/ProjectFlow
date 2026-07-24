<?php

namespace App\Domain\People\Services;

use App\Domain\Organizations\Models\Organization;
use App\Domain\People\Models\Person;
use Illuminate\Database\Eloquent\Collection;

final class PersonDuplicateDetector
{
    /**
     * @return Collection<int, Person>
     */
    public function find(
        Organization $organization,
        ?string $email,
        ?string $phone,
        ?int $exceptPersonId = null,
    ): Collection {
        if ($email === null && $phone === null) {
            return new Collection;
        }

        return Person::query()
            ->forOrganization($organization)
            ->when($exceptPersonId, fn ($query) => $query->whereKeyNot($exceptPersonId))
            ->where(function ($query) use ($email, $phone): void {
                $query
                    ->when($email, fn ($query) => $query->orWhere('primary_email', $email))
                    ->when($phone, fn ($query) => $query->orWhere('primary_phone', $phone));
            })
            ->orderBy('display_name')
            ->get(['id', 'display_name', 'primary_email', 'primary_phone']);
    }
}
