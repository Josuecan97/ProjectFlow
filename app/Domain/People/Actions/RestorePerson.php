<?php

namespace App\Domain\People\Actions;

use App\Domain\Organizations\Support\SubscriptionAccess;
use App\Domain\People\Enums\PersonStatus;
use App\Domain\People\Models\Person;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final class RestorePerson
{
    public function __construct(private readonly SubscriptionAccess $subscriptionAccess) {}

    public function handle(User $actor, Person $person): Person
    {
        Gate::forUser($actor)->authorize('restore', $person);
        $this->subscriptionAccess->authorizeWrites($person->organization);

        DB::transaction(function () use ($person): void {
            $person->restore();
            $person->update(['status' => PersonStatus::Active]);
        });

        return $person->refresh();
    }
}
