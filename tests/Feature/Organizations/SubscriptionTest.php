<?php

use App\Domain\Organizations\Actions\CreateOrganization;
use App\Domain\Organizations\Actions\GrantManualSubscription;
use App\Domain\Organizations\Enums\SubscriptionSource;
use App\Domain\Organizations\Enums\SubscriptionStatus;
use App\Domain\Organizations\Support\SubscriptionAccess;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Auth\Access\AuthorizationException;

beforeEach(function () {
    $this->seed(AccessControlSeeder::class);
});

it('creates a full-access fourteen-day trial by default', function () {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Acme']);
    $subscription = $organization->currentSubscription;

    expect($subscription->status)->toBe(SubscriptionStatus::Trial)
        ->and($subscription->remainingDays())->toBe(14)
        ->and(app(SubscriptionAccess::class)->allowsWrites($organization))->toBeTrue();
});

it('changes an expired trial to read-only and preserves consultation', function () {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Acme']);
    $organization->currentSubscription->update(['ends_at' => now()->subMinute()]);
    $organization->unsetRelation('currentSubscription');

    $access = app(SubscriptionAccess::class);

    expect($access->allowsWrites($organization))->toBeFalse()
        ->and($access->current($organization)->status)->toBe(SubscriptionStatus::Expired)
        ->and($access->current($organization)->events()->where('type', 'expired')->count())->toBe(1);

    expect(fn () => $access->authorizeWrites($organization))
        ->toThrow(AuthorizationException::class);

    $this->actingAs($owner)
        ->withSession(['organization_id' => $organization->id])
        ->get(route('organization.subscription'))
        ->assertOk()
        ->assertSee('solo lectura');
});

it('supports a manual membership assignment with auditable history', function () {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Acme']);

    $subscription = app(GrantManualSubscription::class)->handle(
        $organization,
        $owner,
        now(),
        now()->addMonth(),
        'Asignación administrativa',
    );

    expect($subscription->status)->toBe(SubscriptionStatus::Active)
        ->and($subscription->source)->toBe(SubscriptionSource::Manual)
        ->and($subscription->events()->where('type', 'manual_grant')->exists())->toBeTrue()
        ->and($organization->subscriptions()->where('status', SubscriptionStatus::Cancelled)->exists())->toBeTrue();
});
