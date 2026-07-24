<?php

namespace App\Domain\People\Actions;

use App\Domain\Organizations\Support\SubscriptionAccess;
use App\Domain\People\Models\Person;
use App\Domain\People\Models\PersonRelationship;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

final class RemovePersonContact
{
    public function __construct(private readonly SubscriptionAccess $subscriptionAccess) {}

    public function handle(
        User $actor,
        Person $organizationPerson,
        PersonRelationship $relationship,
    ): void {
        Gate::forUser($actor)->authorize('update', $organizationPerson);
        $this->subscriptionAccess->authorizeWrites($organizationPerson->organization);

        abort_unless(
            $relationship->organization_id === $organizationPerson->organization_id
            && $relationship->parent_person_id === $organizationPerson->id,
            404,
        );

        $relationship->delete();
    }
}
