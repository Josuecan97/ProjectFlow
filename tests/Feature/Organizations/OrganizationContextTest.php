<?php

use App\Domain\Organizations\Enums\OrganizationMemberStatus;
use App\Domain\Organizations\Enums\SystemRole;
use App\Domain\Organizations\Models\Organization;
use App\Domain\Organizations\Models\OrganizationMember;
use App\Domain\Organizations\Models\OrganizationSubscription;
use App\Domain\Organizations\Models\Role;
use App\Domain\Organizations\Support\CurrentOrganization;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Livewire\Volt\Volt;

beforeEach(function () {
    $this->seed(AccessControlSeeder::class);
});
it('automatically selects the only active organization', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->create();
    OrganizationMember::factory()->create([
        'organization_id' => $organization->id,
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSessionHas('organization_id', $organization->id);
});

it('asks the user to select when several organizations are available', function () {
    $user = User::factory()->create();
    OrganizationMember::factory()->count(2)->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('organizations.select'));
});

it('rejects a session organization that does not belong to the user', function () {
    $user = User::factory()->create();
    $authorized = Organization::factory()->create();
    $unauthorized = Organization::factory()->create();

    OrganizationMember::factory()->create([
        'organization_id' => $authorized->id,
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->withSession(['organization_id' => $unauthorized->id])
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSessionHas('organization_id', $authorized->id);
});

it('does not use suspended memberships for tenant access', function () {
    $user = User::factory()->create();

    OrganizationMember::factory()->create([
        'user_id' => $user->id,
        'status' => OrganizationMemberStatus::Suspended,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('organizations.create'));
});

it('updates only the organization resolved in the current context', function () {
    $user = User::factory()->create();
    $selected = Organization::factory()->create(['name' => 'Seleccionada']);
    $other = Organization::factory()->create(['name' => 'Otra']);

    OrganizationMember::factory()->create([
        'organization_id' => $selected->id,
        'user_id' => $user->id,
    ])->roles()->attach(Role::query()->where('code', SystemRole::Owner->value)->sole());
    OrganizationSubscription::factory()->create([
        'organization_id' => $selected->id,
    ]);

    $this->actingAs($user);
    app(CurrentOrganization::class)->set($selected);

    Volt::test('organizations.settings')
        ->set('name', 'Actualizada')
        ->call('updateOrganization')
        ->assertHasNoErrors();

    expect($selected->fresh()->name)->toBe('Actualizada')
        ->and($other->fresh()->name)->toBe('Otra');
});
