<?php

use App\Domain\Organizations\Models\Organization;
use App\Domain\People\Enums\PersonRoleCode;
use App\Domain\People\Enums\PersonStatus;
use App\Domain\People\Enums\PersonType;
use App\Domain\People\Models\Person;
use App\Domain\People\Models\PersonRelationship;
use App\Domain\People\Models\PersonRole;
use Database\Seeders\PersonRoleSeeder;
use Illuminate\Database\QueryException;

beforeEach(function () {
    $this->seed(PersonRoleSeeder::class);
});

it('stores physical and moral people under an organization', function () {
    $organization = Organization::factory()->create();
    $individual = Person::factory()->for($organization)->create();
    $company = Person::factory()->for($organization)->organization()->create();

    expect($individual->type)->toBe(PersonType::Individual)
        ->and($individual->status)->toBe(PersonStatus::Active)
        ->and($company->type)->toBe(PersonType::Organization)
        ->and($organization->people()->count())->toBe(2);
});

it('assigns multiple commercial roles to one person', function () {
    $person = Person::factory()->create();
    $roles = PersonRole::query()
        ->whereIn('code', [PersonRoleCode::Client->value, PersonRoleCode::Supplier->value])
        ->get();

    $person->roles()->sync($roles->modelKeys());

    expect($person->roles)->toHaveCount(2)
        ->and($person->roles->pluck('code')->all())
        ->toContain(PersonRoleCode::Client->value, PersonRoleCode::Supplier->value);
});

it('relates a contact as another person', function () {
    $organization = Organization::factory()->create();
    $company = Person::factory()->for($organization)->organization()->create();
    $contact = Person::factory()->for($organization)->create();

    PersonRelationship::query()->create([
        'organization_id' => $organization->id,
        'parent_person_id' => $company->id,
        'related_person_id' => $contact->id,
        'type' => 'contact',
        'job_title' => 'Dirección',
        'is_primary' => true,
    ]);

    expect($company->contacts()->sole()->related->is($contact))->toBeTrue();
});

it('allows the same tax id in different organizations but not within one', function () {
    $firstOrganization = Organization::factory()->create();
    $secondOrganization = Organization::factory()->create();

    Person::factory()->for($firstOrganization)->create(['tax_id' => 'RFC010101AAA']);
    Person::factory()->for($secondOrganization)->create(['tax_id' => 'RFC010101AAA']);

    expect(fn () => Person::factory()->for($firstOrganization)->create([
        'tax_id' => 'RFC010101AAA',
    ]))->toThrow(QueryException::class);
});

it('archives people using soft deletes without losing their record', function () {
    $person = Person::factory()->create();
    $person->update(['status' => PersonStatus::Archived]);
    $person->delete();

    expect(Person::query()->find($person->id))->toBeNull()
        ->and(Person::withTrashed()->find($person->id)?->status)->toBe(PersonStatus::Archived);
});
