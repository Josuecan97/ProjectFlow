<?php

namespace App\Domain\Organizations\Actions;

use App\Domain\Organizations\Enums\OrganizationInvitationStatus;
use App\Domain\Organizations\Enums\OrganizationMemberStatus;
use App\Domain\Organizations\Models\OrganizationInvitation;
use App\Domain\Organizations\Models\OrganizationMember;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class AcceptOrganizationInvitation
{
    public function handle(User $user, string $token): OrganizationMember
    {
        return DB::transaction(function () use ($user, $token): OrganizationMember {
            $invitation = OrganizationInvitation::query()
                ->where('token_hash', hash('sha256', $token))
                ->lockForUpdate()
                ->firstOrFail();

            if ($invitation->status !== OrganizationInvitationStatus::Pending) {
                throw ValidationException::withMessages(['invitation' => 'La invitación ya no está disponible.']);
            }

            if ($invitation->expires_at->isPast()) {
                $invitation->update(['status' => OrganizationInvitationStatus::Expired]);

                throw ValidationException::withMessages(['invitation' => 'La invitación expiró.']);
            }

            if (! Str::is(Str::lower($invitation->email), Str::lower($user->email))) {
                throw ValidationException::withMessages([
                    'invitation' => 'La invitación pertenece a otro correo electrónico.',
                ]);
            }

            $member = OrganizationMember::withTrashed()->firstOrNew([
                'organization_id' => $invitation->organization_id,
                'user_id' => $user->id,
            ]);

            $member->fill([
                'status' => OrganizationMemberStatus::Active,
                'invited_by_user_id' => $invitation->invited_by_user_id,
                'joined_at' => now(),
            ]);
            $member->deleted_at = null;
            $member->save();
            $member->roles()->sync([$invitation->role_id]);

            $invitation->update([
                'status' => OrganizationInvitationStatus::Accepted,
                'accepted_at' => now(),
            ]);

            return $member;
        });
    }
}
