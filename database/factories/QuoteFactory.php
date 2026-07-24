<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Organizations\Models\Organization;
use App\Domain\People\Models\Person;
use App\Domain\Quotes\Enums\QuoteStatus;
use App\Domain\Quotes\Models\Quote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quote>
 */
final class QuoteFactory extends Factory
{
    protected $model = Quote::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'person_id' => fn (array $attributes): int => Person::factory()->create([
                'organization_id' => $attributes['organization_id'],
            ])->id,
            'number' => 'COT-'.fake()->unique()->numerify('######'),
            'status' => QuoteStatus::Draft,
        ];
    }

    public function forOrganization(Organization $organization, ?Person $person = null): static
    {
        $person ??= Person::factory()->for($organization)->create();

        return $this->state([
            'organization_id' => $organization->id,
            'person_id' => $person->id,
        ]);
    }
}
