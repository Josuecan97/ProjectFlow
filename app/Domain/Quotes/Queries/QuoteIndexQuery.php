<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Queries;

use App\Domain\Organizations\Models\Organization;
use App\Domain\Quotes\Enums\QuoteStatus;
use App\Domain\Quotes\Models\Quote;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class QuoteIndexQuery
{
    public function paginate(
        Organization $organization,
        string $search = '',
        ?QuoteStatus $status = null,
        int $perPage = 15,
    ): LengthAwarePaginator {
        $search = trim($search);

        return Quote::query()
            ->where('organization_id', $organization->id)
            ->with(['person:id,display_name', 'currentVersion:id,quote_id,version_number,expires_on,currency,total'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($search !== '', function ($query) use ($search): void {
                $term = '%'.addcslashes($search, '%_\\').'%';
                $query->where(function ($query) use ($term): void {
                    $query->where('number', 'like', $term)
                        ->orWhereHas('person', fn ($query) => $query->where('display_name', 'like', $term))
                        ->orWhereHas('currentVersion', fn ($query) => $query->where('title', 'like', $term));
                });
            })
            ->latest('id')
            ->paginate($perPage);
    }
}
