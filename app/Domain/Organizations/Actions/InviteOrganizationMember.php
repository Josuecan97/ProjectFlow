<?php

namespace App\Domain\Organizations\Actions;

use App\Domain\Organizations\Enums\OrganizationInvitationStatus;
use App\Domain\Organizations\Enums\SystemRole;
use App\Domain\Organizations\Models\Organization;
use App\Domain\Organizations\Models\OrganizationInvitation;
use App\Domain\Organizations\Models\Role;
use App\Domain\Organizations\Notifications\OrganizationInvitationNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class InviteOrganizationMember
{
    public function handle(
        Organization $organization,
        User $inviter,
        string $email,
        Role $role,
    ): OrganizationInvitation {
        $normalizedEmail = Str::lower(trim($email));

        if ($role->organization_id !== null) {
            throw ValidationException::withMessages([
                'roleId' => 'El rol seleccionado no está disponible para esta organización.',
            ]);
        }

        if ($role->code === SystemRole::Owner->value) {
            throw ValidationException::withMessages([
                'roleId' => 'La propiedad se transfiere; no puede asignarse por invitación.',
            ]);
        }

        $alreadyMember = $organization->members()
            ->whereHas('user', fn ($query) => $query->where('email', $normalizedEmail))
            ->exists();

        if ($alreadyMember) {
            throw ValidationException::withMessages([
                'email' => 'Este usuario ya pertenece a la organización.',
            ]);
        }

        $token = Str::random(64);

        $invitation = OrganizationInvitation::query()->updateOrCreate([
            'organization_id' => $organization->id,
            'email' => $normalizedEmail,
            'status' => OrganizationInvitationStatus::Pending,
        ], [
            'role_id' => $role->id,
            'invited_by_user_id' => $inviter->id,
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addDays(7),
            'accepted_at' => null,
        ]);

        Notification::route('mail', $normalizedEmail)
            ->notify(new OrganizationInvitationNotification($invitation, $token));

        return $invitation;
    }
}
