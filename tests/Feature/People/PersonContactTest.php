<?php

use App\Domain\Organizations\Actions\CreateOrganization;
use App\Domain\People\Actions\RemovePersonContact;
use App\Domain\People\Actions\SavePersonContact;
use App\Domain\People\Models\Person;
use App\Domain\People\Models\PersonRelationship;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

beforeEach(function () {
    $this->seed(AccessControlSeeder::class);
});

it('associates contacts and keeps only one primary contact', function () {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Acme']);
    $company = Person::factory()->for($organization)->organization()->create();
    $firstContact = Person::factory()->for($organization)->create();
    $secondContact = Person::factory()->for($organization)->create();

    app(SavePersonContact::class)->handle($owner, $company, $firstContact, [
        'job_title' => 'Dirección',
        'is_primary' => true,
    ]);
    $secondRelationship = app(SavePersonContact::class)->handle($owner, $company, $secondContact, [
        'job_title' => 'Operaciones',
        'is_primary' => true,
    ]);

    expect($company->contacts()->count())->toBe(2)
        ->and($company->contacts()->where('is_primary', true)->sole()->is($secondRelationship))
        ->toBeTrue();
});

it('updates an existing contact relation without duplicating it', function () {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Acme']);
    $company = Person::factory()->for($organization)->organization()->create();
    $contact = Person::factory()->for($organization)->create();

    app(SavePersonContact::class)->handle($owner, $company, $contact, ['job_title' => 'Ventas']);
    app(SavePersonContact::class)->handle($owner, $company, $contact, ['job_title' => 'Dirección']);

    expect($company->contacts()->count())->toBe(1)
        ->and($company->contacts()->sole()->job_title)->toBe('Dirección');
});

it('rejects self relationships, physical parents and cross-organization contacts', function (
    string $scenario,
) {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Acme']);
    $otherOrganization = app(CreateOrganization::class)->handle($owner, ['name' => 'Otra']);
    $company = Person::factory()->for($organization)->organization()->create();
    $individual = Person::factory()->for($organization)->create();
    $external = Person::factory()->for($otherOrganization)->create();

    [$parent, $contact] = match ($scenario) {
        'self' => [$company, $company],
        'physical parent' => [$individual, $company],
        'cross tenant' => [$company, $external],
    };

    expect(fn () => app(SavePersonContact::class)->handle(
        $owner,
        $parent,
        $contact,
        [],
    ))->toThrow(ValidationException::class);
})->with(['self', 'physical parent', 'cross tenant']);

it('removes only a relationship owned by the selected parent', function () {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Acme']);
    $company = Person::factory()->for($organization)->organization()->create();
    $otherCompany = Person::factory()->for($organization)->organization()->create();
    $contact = Person::factory()->for($organization)->create();
    $relationship = app(SavePersonContact::class)->handle($owner, $company, $contact, []);

    expect(fn () => app(RemovePersonContact::class)->handle(
        $owner,
        $otherCompany,
        $relationship,
    ))->toThrow(NotFoundHttpException::class);

    app(RemovePersonContact::class)->handle($owner, $company, $relationship);

    expect(PersonRelationship::query()->count())->toBe(0);
});

it('enforces relationship tenant integrity at the database boundary', function () {
    $organization = app(CreateOrganization::class)->handle(
        User::factory()->create(),
        ['name' => 'Acme'],
    );
    $otherOrganization = app(CreateOrganization::class)->handle(
        User::factory()->create(),
        ['name' => 'Otra'],
    );
    $company = Person::factory()->for($organization)->organization()->create();
    $externalContact = Person::factory()->for($otherOrganization)->create();

    expect(fn () => DB::table('person_relationships')->insert([
        'organization_id' => $organization->id,
        'parent_person_id' => $company->id,
        'related_person_id' => $externalContact->id,
        'type' => 'contact',
        'is_primary' => false,
    ]))->toThrow(QueryException::class);

    expect(PersonRelationship::query()->count())->toBe(0);
});

it('rejects self relationships at the database boundary', function () {
    $organization = app(CreateOrganization::class)->handle(
        User::factory()->create(),
        ['name' => 'Acme'],
    );
    $company = Person::factory()->for($organization)->organization()->create();

    expect(fn () => DB::table('person_relationships')->insert([
        'organization_id' => $organization->id,
        'parent_person_id' => $company->id,
        'related_person_id' => $company->id,
        'type' => 'contact',
        'is_primary' => false,
    ]))->toThrow(QueryException::class);

    expect(PersonRelationship::query()->count())->toBe(0);
});
