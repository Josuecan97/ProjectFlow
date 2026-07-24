<?php

namespace App\Domain\Organizations\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['organization_id', 'name', 'code', 'is_system'])]
final class Role extends Model
{
    protected function casts(): array
    {
        return ['is_system' => 'boolean'];
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(
            OrganizationMember::class,
            'organization_member_role',
        );
    }
}
