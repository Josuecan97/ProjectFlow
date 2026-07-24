<?php

use App\Domain\Organizations\Actions\CreateOrganization;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;

beforeEach(function () {
    $this->seed(AccessControlSeeder::class);
});

it('renders every sprint zero organization screen for an owner', function () {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Empresa visible']);

    $this->actingAs($owner)->withSession(['organization_id' => $organization->id]);

    foreach ([
        route('dashboard'),
        route('organization.settings'),
        route('organization.members'),
        route('organization.roles'),
        route('organization.subscription'),
        route('profile.edit'),
        route('organizations.select'),
    ] as $url) {
        $this->get($url)->assertOk();
    }
});
