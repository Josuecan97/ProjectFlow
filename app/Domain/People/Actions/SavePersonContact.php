<?php

namespace App\Domain\People\Actions;

use App\Domain\Organizations\Support\SubscriptionAccess;
use App\Domain\People\Enums\PersonRelationshipType;
use App\Domain\People\Enums\PersonType;
use App\Domain\People\Models\Person;
use App\Domain\People\Models\PersonRelationship;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

final class SavePersonContact
{
    public function __construct(private readonly SubscriptionAccess $subscriptionAccess) {}

    /**
     * @param  array{job_title?: ?string, is_primary?: bool, notes?: ?string}  $attributes
     */
    public function handle(
        User $actor,
        Person $organizationPerson,
        Person $contact,
        array $attributes,
    ): PersonRelationship {
        Gate::forUser($actor)->authorize('update', $organizationPerson);
        $this->subscriptionAccess->authorizeWrites($organizationPerson->organization);

        if ($organizationPerson->type !== PersonType::Organization) {
            throw ValidationException::withMessages([
                'related_person_id' => 'Los contactos solo pueden asociarse a una Persona moral.',
            ]);
        }

        if (
            $organizationPerson->is($contact)
            || $organizationPerson->organization_id !== $contact->organization_id
        ) {
            throw ValidationException::withMessages([
                'related_person_id' => 'El contacto debe ser otra Persona de la misma Organización.',
            ]);
        }

        return DB::transaction(function () use ($organizationPerson, $contact, $attributes) {
            if ((bool) ($attributes['is_primary'] ?? false)) {
                $organizationPerson->contacts()
                    ->lockForUpdate()
                    ->update(['is_primary' => false]);
            }

            return PersonRelationship::query()->updateOrCreate([
                'organization_id' => $organizationPerson->organization_id,
                'parent_person_id' => $organizationPerson->id,
                'related_person_id' => $contact->id,
                'type' => PersonRelationshipType::Contact,
            ], [
                'job_title' => $this->nullableString($attributes['job_title'] ?? null),
                'is_primary' => (bool) ($attributes['is_primary'] ?? false),
                'notes' => $this->nullableString($attributes['notes'] ?? null),
            ]);
        });
    }

    private function nullableString(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : null;

        return $value === '' ? null : $value;
    }
}
