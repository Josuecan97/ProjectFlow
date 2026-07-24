<?php

use App\Domain\Organizations\Actions\CreateOrganization;
use App\Domain\Organizations\Enums\SystemRole;
use App\Domain\Organizations\Models\Organization;
use App\Domain\Organizations\Models\Role;
use App\Domain\People\Actions\ArchivePerson;
use App\Domain\People\Actions\CreatePerson;
use App\Domain\People\Actions\RestorePerson;
use App\Domain\People\Actions\UpdatePerson;
use App\Domain\People\Enums\PersonRoleCode;
use App\Domain\People\Enums\PersonStatus;
use App\Domain\People\Models\Person;
use App\Domain\People\Models\PersonRole;
use App\Domain\People\Queries\PersonIndexQuery;
use App\Domain\People\Services\PersonDuplicateDetector;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Database\Seeders\PersonRoleSeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->seed([AccessControlSeeder::class, PersonRoleSeeder::class]);
});

it('creates and normalizes a person through the domain action', function () {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Acme']);
    $roles = PersonRole::query()
        ->whereIn('code', [PersonRoleCode::Client->value, PersonRoleCode::Supplier->value])
        ->pluck('id')
        ->all();

    $person = app(CreatePerson::class)->handle($owner, $organization, [
        'type' => 'individual',
        'first_name' => '  Ana ',
        'last_name' => ' López ',
        'tax_id' => ' lol-a010101aa1 ',
        'primary_email' => ' ANA@EXAMPLE.TEST ',
        'primary_phone' => '+52 (999) 123-4567',
        'address' => ['country' => 'mx'],
    ], $roles);

    expect($person->display_name)->toBe('Ana López')
        ->and($person->tax_id)->toBe('LOLA010101AA1')
        ->and($person->primary_email)->toBe('ana@example.test')
        ->and($person->primary_phone)->toBe('+529991234567')
        ->and($person->address['country'])->toBe('MX')
        ->and($person->roles)->toHaveCount(2);
});

it('rejects invalid commercial role ids at the domain boundary', function () {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Acme']);

    expect(fn () => app(CreatePerson::class)->handle($owner, $organization, [
        'type' => 'individual',
        'first_name' => 'Ana',
    ], [999999]))->toThrow(ValidationException::class);

    expect($organization->people()->count())->toBe(0);
});

it('updates, archives and restores a person through authorized actions', function () {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Acme']);
    $person = Person::factory()->for($organization)->create();
    $role = PersonRole::query()->where('code', PersonRoleCode::Partner->value)->sole();

    $person = app(UpdatePerson::class)->handle($owner, $person, [
        'type' => 'organization',
        'display_name' => 'Nueva empresa',
        'legal_name' => 'Nueva Empresa, S.A.',
    ], [$role->id]);

    expect($person->display_name)->toBe('Nueva empresa')
        ->and($person->first_name)->toBeNull()
        ->and($person->roles()->sole()->is($role))->toBeTrue();

    app(ArchivePerson::class)->handle($owner, $person);
    expect(Person::query()->find($person->id))->toBeNull()
        ->and(Person::withTrashed()->findOrFail($person->id)->status)->toBe(PersonStatus::Archived);

    $restored = app(RestorePerson::class)->handle($owner, $person);
    expect($restored->trashed())->toBeFalse()
        ->and($restored->status)->toBe(PersonStatus::Active);
});

it('denies cross-organization access even when the user owns another organization', function () {
    $firstOwner = User::factory()->create();
    $secondOwner = User::factory()->create();
    $firstOrganization = app(CreateOrganization::class)->handle($firstOwner, ['name' => 'Primera']);
    $secondOrganization = app(CreateOrganization::class)->handle($secondOwner, ['name' => 'Segunda']);
    $person = Person::factory()->for($firstOrganization)->create();

    expect(fn () => app(UpdatePerson::class)->handle($secondOwner, $person, [
        'type' => 'individual',
        'first_name' => 'Intruso',
    ]))->toThrow(AuthorizationException::class);

    expect(fn () => app(ArchivePerson::class)->handle($secondOwner, $person))
        ->toThrow(AuthorizationException::class);

    expect($secondOrganization->people()->count())->toBe(0);
});

it('blocks writes for an expired membership while preserving reads', function () {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Acme']);
    $organization->currentSubscription->update(['ends_at' => now()->subMinute()]);
    $organization->unsetRelation('currentSubscription');

    expect(fn () => app(CreatePerson::class)->handle($owner, $organization, [
        'type' => 'individual',
        'first_name' => 'Ana',
    ]))->toThrow(AuthorizationException::class);

    expect($organization->people()->count())->toBe(0);
});

it('scopes listing, search, roles and archive filters to one organization', function () {
    $organization = Organization::factory()->create();
    $otherOrganization = Organization::factory()->create();
    $clientRole = PersonRole::query()->where('code', PersonRoleCode::Client->value)->sole();

    $visible = Person::factory()->for($organization)->create([
        'display_name' => 'Cliente visible',
        'primary_email' => 'visible@example.test',
    ]);
    $visible->roles()->attach($clientRole);
    Person::factory()->for($otherOrganization)->create([
        'display_name' => 'Cliente externo',
        'primary_email' => 'visible@example.test',
    ]);
    Person::factory()->for($organization)->archived()->create(['display_name' => 'Archivada']);

    $results = app(PersonIndexQuery::class)->paginate(
        $organization,
        search: 'visible',
        role: PersonRoleCode::Client->value,
    );
    $archived = app(PersonIndexQuery::class)->paginate(
        $organization,
        status: PersonStatus::Archived,
    );

    expect($results->total())->toBe(1)
        ->and($results->first()->is($visible))->toBeTrue()
        ->and($archived->total())->toBe(1);
});

it('warns about matching email or phone only inside the organization', function () {
    $organization = Organization::factory()->create();
    $otherOrganization = Organization::factory()->create();
    $person = Person::factory()->for($organization)->create([
        'primary_email' => 'same@example.test',
        'primary_phone' => '9991234567',
    ]);
    Person::factory()->for($otherOrganization)->create([
        'primary_email' => 'same@example.test',
    ]);

    $matches = app(PersonDuplicateDetector::class)->find(
        $organization,
        'same@example.test',
        null,
    );

    expect($matches)->toHaveCount(1)
        ->and($matches->first()->is($person))->toBeTrue();
});

it('respects the commercial permissions assigned to internal roles', function () {
    $owner = User::factory()->create();
    $viewer = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Acme']);
    $viewerMember = $organization->members()->create([
        'user_id' => $viewer->id,
        'status' => 'active',
        'joined_at' => now(),
    ]);
    $viewerMember->roles()->attach(
        Role::query()->where('code', SystemRole::Viewer->value)->sole(),
    );

    expect($viewer->can('viewAny', [Person::class, $organization]))->toBeTrue()
        ->and($viewer->can('create', [Person::class, $organization]))->toBeFalse();
});
