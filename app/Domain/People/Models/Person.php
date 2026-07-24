<?php

namespace App\Domain\People\Models;

use App\Domain\Organizations\Models\Organization;
use App\Domain\People\Enums\PersonStatus;
use App\Domain\People\Enums\PersonType;
use Database\Factories\PersonFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property PersonType $type
 * @property-read Organization $organization
 */
#[Fillable([
    'organization_id',
    'type',
    'display_name',
    'legal_name',
    'first_name',
    'last_name',
    'tax_id',
    'curp',
    'primary_email',
    'primary_phone',
    'website',
    'address',
    'notes',
    'status',
])]
final class Person extends Model
{
    /** @use HasFactory<PersonFactory> */
    use HasFactory, SoftDeletes;

    protected static function newFactory(): PersonFactory
    {
        return PersonFactory::new();
    }

    protected function casts(): array
    {
        return [
            'type' => PersonType::class,
            'status' => PersonStatus::class,
            'address' => 'array',
        ];
    }

    /** @return BelongsTo<Organization, $this> */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /** @return BelongsToMany<PersonRole, $this> */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(PersonRole::class, 'person_role');
    }

    /** @return HasMany<PersonRelationship, $this> */
    public function contacts(): HasMany
    {
        return $this->hasMany(PersonRelationship::class, 'parent_person_id');
    }

    /** @return HasMany<PersonRelationship, $this> */
    public function contactFor(): HasMany
    {
        return $this->hasMany(PersonRelationship::class, 'related_person_id');
    }

    public function scopeForOrganization(Builder $query, Organization|int $organization): Builder
    {
        $organizationId = $organization instanceof Organization
            ? $organization->getKey()
            : $organization;

        return $query->where('organization_id', $organizationId);
    }
}
