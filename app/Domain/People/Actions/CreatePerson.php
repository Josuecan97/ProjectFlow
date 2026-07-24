<?php

namespace App\Domain\People\Actions;

use App\Domain\Organizations\Models\Organization;
use App\Domain\Organizations\Support\SubscriptionAccess;
use App\Domain\People\Enums\PersonStatus;
use App\Domain\People\Models\Person;
use App\Domain\People\Services\PersonDataNormalizer;
use App\Domain\People\Services\PersonRoleResolver;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final class CreatePerson
{
    public function __construct(
        private readonly PersonDataNormalizer $normalizer,
        private readonly PersonRoleResolver $roleResolver,
        private readonly SubscriptionAccess $subscriptionAccess,
    ) {}

    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<int, int|string>  $roleIds
     */
    public function handle(User $actor, Organization $organization, array $attributes, array $roleIds = []): Person
    {
        Gate::forUser($actor)->authorize('create', [Person::class, $organization]);
        $this->subscriptionAccess->authorizeWrites($organization);

        return DB::transaction(function () use ($organization, $attributes, $roleIds): Person {
            $person = $organization->people()->create([
                ...$this->normalizer->normalize($attributes),
                'status' => PersonStatus::Active,
            ]);

            $person->roles()->sync($this->roleResolver->resolve($roleIds));

            return $person->load('roles');
        });
    }
}
