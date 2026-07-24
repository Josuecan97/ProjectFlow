<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Services;

use App\Domain\Organizations\Models\Organization;
use App\Domain\People\Enums\PersonRoleCode;
use App\Domain\People\Enums\PersonStatus;
use App\Domain\People\Models\Person;
use App\Domain\Quotes\Support\QuoteDraftRules;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class QuoteDraftValidator
{
    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public function validate(Organization $organization, array $attributes): array
    {
        $validated = Validator::make(
            $attributes,
            QuoteDraftRules::forOrganization($organization->id),
        )->validate();

        $person = Person::query()
            ->where('organization_id', $organization->id)
            ->where('status', PersonStatus::Active)
            ->findOrFail($validated['person_id']);

        if (! $person->roles()->where('code', PersonRoleCode::Client->value)->exists()) {
            throw ValidationException::withMessages([
                'person_id' => __('La Persona debe tener el rol Cliente. Confirma la asignación antes de continuar.'),
            ]);
        }

        $validated['currency'] = strtoupper((string) $validated['currency']);

        return $validated;
    }
}
