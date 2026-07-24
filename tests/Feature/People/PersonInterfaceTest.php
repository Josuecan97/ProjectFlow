<?php

use App\Domain\Organizations\Actions\CreateOrganization;
use App\Domain\Organizations\Support\CurrentOrganization;
use App\Domain\People\Enums\PersonRoleCode;
use App\Domain\People\Models\Person;
use App\Domain\People\Models\PersonRole;
use App\Http\Middleware\EnsureOrganizationSelected;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Database\Seeders\PersonRoleSeeder;
use Livewire\Livewire;
use Livewire\Volt\Volt;

beforeEach(function () {
    $this->seed([AccessControlSeeder::class, PersonRoleSeeder::class]);
});

it('renders the complete people interface for an owner', function () {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Acme']);
    $person = Person::factory()->for($organization)->create();

    $this->actingAs($owner)->withSession(['organization_id' => $organization->id]);

    foreach ([
        route('people.index'),
        route('people.create'),
        route('people.show', $person),
        route('people.edit', $person),
    ] as $url) {
        $this->get($url)->assertOk();
    }
});

it('creates a person from the Volt form', function () {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Acme']);
    $clientRole = PersonRole::query()->where('code', PersonRoleCode::Client->value)->sole();
    $this->actingAs($owner);
    app(CurrentOrganization::class)->set($organization);

    Volt::test('people.create')
        ->set('type', 'individual')
        ->set('first_name', 'Ana')
        ->set('last_name', 'López')
        ->set('tax_id', 'LOLA010101AA1')
        ->set('primary_email', 'ana@example.test')
        ->set('role_ids', [$clientRole->id])
        ->call('save')
        ->assertHasNoErrors();

    $person = Person::query()->sole();

    expect($person->display_name)->toBe('Ana López')
        ->and($person->roles()->sole()->code)->toBe(PersonRoleCode::Client->value);
});

it('edits, archives and restores from Volt screens', function () {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Acme']);
    $person = Person::factory()->for($organization)->create();
    $this->actingAs($owner);
    app(CurrentOrganization::class)->set($organization);

    Volt::test('people.edit', ['person' => $person])
        ->set('first_name', 'Nombre actualizado')
        ->call('save')
        ->assertHasNoErrors();

    expect($person->fresh()->display_name)->toStartWith('Nombre actualizado');

    Volt::test('people.index')
        ->call('archive', $person->id)
        ->assertHasNoErrors()
        ->set('status', 'archived')
        ->call('restore', $person->id)
        ->assertHasNoErrors();

    expect($person->fresh()->trashed())->toBeFalse();
});

it('returns not found for a person outside the selected organization', function () {
    $owner = User::factory()->create();
    $selected = app(CreateOrganization::class)->handle($owner, ['name' => 'Seleccionada']);
    $other = app(CreateOrganization::class)->handle($owner, ['name' => 'Otra']);
    $externalPerson = Person::factory()->for($other)->create();

    $this->actingAs($owner)
        ->withSession(['organization_id' => $selected->id])
        ->get(route('people.show', $externalPerson))
        ->assertNotFound();
});

it('manages a company contact from its record', function () {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Acme']);
    $company = Person::factory()->for($organization)->organization()->create();
    $contact = Person::factory()->for($organization)->create();
    $this->actingAs($owner);
    app(CurrentOrganization::class)->set($organization);

    Volt::test('people.show', ['person' => $company])
        ->set('related_person_id', (string) $contact->id)
        ->set('job_title', 'Dirección general')
        ->set('is_primary', true)
        ->call('saveContact')
        ->assertHasNoErrors()
        ->assertSee($contact->display_name);

    expect($company->contacts()->sole()->related->is($contact))->toBeTrue()
        ->and($company->contacts()->sole()->is_primary)->toBeTrue();
});

it('keeps records readable but hides mutation screens after subscription expiry', function () {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Acme']);
    $person = Person::factory()->for($organization)->create();
    $organization->currentSubscription->update(['ends_at' => now()->subMinute()]);
    $organization->unsetRelation('currentSubscription');
    $this->actingAs($owner)->withSession(['organization_id' => $organization->id]);

    $this->get(route('people.index'))
        ->assertOk()
        ->assertDontSee('Nueva Persona');
    $this->get(route('people.show', $person))
        ->assertOk()
        ->assertDontSee('Editar');
    $this->get(route('people.create'))->assertForbidden();
    $this->get(route('people.edit', $person))->assertForbidden();
});

it('registers organization context middleware for Livewire update requests', function () {
    expect(Livewire::getPersistentMiddleware())
        ->toContain(EnsureOrganizationSelected::class);
});
