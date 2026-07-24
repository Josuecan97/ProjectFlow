<?php

namespace App\Domain\People\Actions;

use App\Domain\Organizations\Support\SubscriptionAccess;
use App\Domain\People\Models\Person;
use App\Domain\People\Services\PersonDataNormalizer;
use App\Domain\People\Services\PersonRoleResolver;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final class UpdatePerson
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
    public function handle(User $actor, Person $person, array $attributes, array $roleIds = []): Person
    {
        Gate::forUser($actor)->authorize('update', $person);
        $this->subscriptionAccess->authorizeWrites($person->organization);

        return DB::transaction(function () use ($person, $attributes, $roleIds): Person {
            $person->update($this->normalizer->normalize($attributes));
            $person->roles()->sync($this->roleResolver->resolve($roleIds));

            return $person->refresh()->load('roles');
        });
    }
}
