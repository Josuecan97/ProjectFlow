<?php

namespace App\Domain\People\Queries;

use App\Domain\Organizations\Models\Organization;
use App\Domain\People\Enums\PersonStatus;
use App\Domain\People\Enums\PersonType;
use App\Domain\People\Models\Person;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class PersonIndexQuery
{
    public function paginate(
        Organization $organization,
        string $search = '',
        ?PersonType $type = null,
        ?string $role = null,
        PersonStatus $status = PersonStatus::Active,
        int $perPage = 15,
    ): LengthAwarePaginator {
        $search = trim($search);

        return Person::query()
            ->forOrganization($organization)
            ->with('roles')
            ->when(
                $status === PersonStatus::Archived,
                fn ($query) => $query->onlyTrashed(),
            )
            ->where('status', $status)
            ->when($type, fn ($query) => $query->where('type', $type))
            ->when(
                $role,
                fn ($query) => $query->whereHas('roles', fn ($query) => $query->where('code', $role)),
            )
            ->when($search !== '', function ($query) use ($search): void {
                $term = '%'.addcslashes($search, '%_\\').'%';

                $query->where(function ($query) use ($term): void {
                    $query->where('display_name', 'like', $term)
                        ->orWhere('legal_name', 'like', $term)
                        ->orWhere('tax_id', 'like', $term)
                        ->orWhere('primary_email', 'like', $term)
                        ->orWhere('primary_phone', 'like', $term);
                });
            })
            ->orderBy('display_name')
            ->paginate($perPage);
    }
}
