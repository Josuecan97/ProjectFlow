<?php

use App\Domain\Organizations\Actions\AcceptOrganizationInvitation;
use App\Domain\Organizations\Actions\CreateOrganization;
use App\Domain\Organizations\Actions\InviteOrganizationMember;
use App\Domain\Organizations\Enums\OrganizationMemberStatus;
use App\Domain\Organizations\Enums\SystemRole;
use App\Domain\Organizations\Models\Role;
use App\Domain\Organizations\Notifications\OrganizationInvitationNotification;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->seed(AccessControlSeeder::class);
});

it('assigns the protected owner role when creating an organization', function () {
    $user = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($user, ['name' => 'Acme']);

    expect($organization->members()->sole()->roles()->sole()->code)
        ->toBe(SystemRole::Owner->value);
});

it('isolates permissions between organizations', function () {
    $owner = User::factory()->create();
    $outsider = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Acme']);
    app(CreateOrganization::class)->handle($outsider, ['name' => 'Otra']);

    expect(Gate::forUser($owner)->allows('manageMembers', $organization))->toBeTrue()
        ->and(Gate::forUser($outsider)->allows('manageMembers', $organization))->toBeFalse();
});

it('invites and accepts a member using a hashed single-use token', function () {
    Notification::fake();
    $owner = User::factory()->create();
    $guest = User::factory()->create(['email' => 'guest@example.test']);
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Acme']);
    $role = Role::query()->where('code', SystemRole::Collaborator->value)->sole();

    $invitation = app(InviteOrganizationMember::class)->handle(
        $organization,
        $owner,
        $guest->email,
        $role,
    );

    Notification::assertSentOnDemand(
        OrganizationInvitationNotification::class,
        function ($notification) use ($guest, &$token): bool {
            $url = $notification->toMail((object) [])->actionUrl;
            $token = basename(parse_url($url, PHP_URL_PATH));

            return str_contains($url, '/invitations/')
                && $guest->email === 'guest@example.test';
        },
    );

    expect($invitation->token_hash)->not->toBe($token);

    $member = app(AcceptOrganizationInvitation::class)->handle($guest, $token);

    expect($member->status)->toBe(OrganizationMemberStatus::Active)
        ->and($member->roles()->sole()->code)->toBe(SystemRole::Collaborator->value);

    expect(fn () => app(AcceptOrganizationInvitation::class)->handle($guest, $token))
        ->toThrow(ValidationException::class);
});

it('rejects accepting an invitation with a different email', function () {
    Notification::fake();
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Acme']);
    $role = Role::query()->where('code', SystemRole::Viewer->value)->sole();

    app(InviteOrganizationMember::class)->handle(
        $organization,
        $owner,
        'invited@example.test',
        $role,
    );

    Notification::assertSentOnDemand(
        OrganizationInvitationNotification::class,
        function ($notification) use (&$token): bool {
            $token = basename(parse_url($notification->toMail((object) [])->actionUrl, PHP_URL_PATH));

            return true;
        },
    );

    $differentUser = User::factory()->create(['email' => 'other@example.test']);

    expect(fn () => app(AcceptOrganizationInvitation::class)->handle($differentUser, $token))
        ->toThrow(ValidationException::class);
});
