<?php

namespace App\Domain\People\Actions;

use App\Domain\Organizations\Support\SubscriptionAccess;
use App\Domain\People\Enums\PersonStatus;
use App\Domain\People\Models\Person;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final class ArchivePerson
{
    public function __construct(private readonly SubscriptionAccess $subscriptionAccess) {}

    public function handle(User $actor, Person $person): void
    {
        Gate::forUser($actor)->authorize('archive', $person);
        $this->subscriptionAccess->authorizeWrites($person->organization);

        DB::transaction(function () use ($person): void {
            $person->update(['status' => PersonStatus::Archived]);
            $person->delete();
        });
    }
}
