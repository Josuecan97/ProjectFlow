<?php

use App\Domain\Organizations\Actions\CreateOrganization;
use App\Domain\Organizations\Enums\OrganizationMemberStatus;
use App\Domain\Organizations\Enums\SubscriptionSource;
use App\Domain\Organizations\Enums\SubscriptionStatus;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Livewire\Volt\Volt;

beforeEach(function () {
    $this->seed(AccessControlSeeder::class);
});

it('creates an organization with its owner and trial atomically', function () {
    $user = User::factory()->create();

    $organization = app(CreateOrganization::class)->handle($user, [
        'name' => 'Empresa Demo',
    ]);

    $member = $organization->members()->sole();
    $subscription = $organization->subscriptions()->sole();

    expect($member->user_id)->toBe($user->id)
        ->and($member->status)->toBe(OrganizationMemberStatus::Active)
        ->and($subscription->status)->toBe(SubscriptionStatus::Trial)
        ->and($subscription->source)->toBe(SubscriptionSource::System)
        ->and($subscription->starts_at->diffInDays($subscription->ends_at))->toBe(14.0)
        ->and($subscription->events()->count())->toBe(1);
});

it('redirects verified users without organizations to onboarding', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('organizations.create'));
});

it('creates an organization from onboarding', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Volt::test('organizations.create')
        ->set('name', 'Empresa desde onboarding')
        ->set('timezone', 'America/Merida')
        ->set('currency', 'MXN')
        ->call('createOrganization')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertDatabaseHas('organizations', ['name' => 'Empresa desde onboarding']);
    $this->assertDatabaseHas('organization_members', ['user_id' => $user->id]);
    $this->assertDatabaseHas('organization_subscriptions', ['status' => 'trial']);
});
