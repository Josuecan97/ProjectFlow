<?php

namespace App\Domain\People\Services;

use App\Domain\People\Models\PersonRole;
use Illuminate\Validation\ValidationException;

final class PersonRoleResolver
{
    /**
     * @param  array<int, int|string>  $roleIds
     * @return array<int, int>
     */
    public function resolve(array $roleIds): array
    {
        $requestedIds = collect($roleIds)
            ->map(fn (int|string $id): int => (int) $id)
            ->unique()
            ->values();

        $validIds = PersonRole::query()
            ->where('is_system', true)
            ->whereKey($requestedIds)
            ->pluck('id');

        if ($validIds->count() !== $requestedIds->count()) {
            throw ValidationException::withMessages([
                'role_ids' => __('Uno o más roles comerciales no son válidos.'),
            ]);
        }

        return $validIds->all();
    }
}
