<?php

namespace App\Domain\Organizations\Models;

use App\Domain\Organizations\Enums\OrganizationInvitationStatus;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property OrganizationInvitationStatus $status
 * @property CarbonInterface $expires_at
 * @property-read Organization $organization
 */
#[Fillable([
    'organization_id',
    'role_id',
    'invited_by_user_id',
    'email',
    'token_hash',
    'status',
    'expires_at',
    'accepted_at',
])]
final class OrganizationInvitation extends Model
{
    protected function casts(): array
    {
        return [
            'status' => OrganizationInvitationStatus::class,
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Organization, $this> */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /** @return BelongsTo<Role, $this> */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /** @return BelongsTo<User, $this> */
    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_user_id');
    }
}
