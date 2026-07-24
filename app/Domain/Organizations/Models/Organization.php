<?php

namespace App\Domain\Organizations\Models;

use App\Domain\People\Models\Person;
use App\Models\User;
use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $name
 * @property-read OrganizationSubscription|null $currentSubscription
 */
#[Fillable([
    'name',
    'legal_name',
    'tax_id',
    'email',
    'phone',
    'timezone',
    'locale',
    'currency',
    'logo_path',
    'settings',
])]
final class Organization extends Model
{
    /** @use HasFactory<OrganizationFactory> */
    use HasFactory;

    protected static function newFactory(): OrganizationFactory
    {
        return OrganizationFactory::new();
    }

    protected function casts(): array
    {
        return [
            'settings' => 'array',
        ];
    }

    /** @return HasMany<OrganizationMember, $this> */
    public function members(): HasMany
    {
        return $this->hasMany(OrganizationMember::class);
    }

    /** @return BelongsToMany<User, $this> */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_members')
            ->withPivot(['id', 'status', 'joined_at', 'deleted_at'])
            ->withTimestamps();
    }

    /** @return HasMany<OrganizationSubscription, $this> */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(OrganizationSubscription::class);
    }

    /** @return HasOne<OrganizationSubscription, $this> */
    public function currentSubscription(): HasOne
    {
        return $this->hasOne(OrganizationSubscription::class)->latestOfMany();
    }

    /** @return HasMany<OrganizationInvitation, $this> */
    public function invitations(): HasMany
    {
        return $this->hasMany(OrganizationInvitation::class);
    }

    /** @return HasMany<Person, $this> */
    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }
}
