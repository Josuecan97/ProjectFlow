<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Models;

use App\Domain\Organizations\Models\Organization;
use App\Domain\Organizations\Models\OrganizationMember;
use App\Domain\People\Models\Person;
use App\Domain\Quotes\Enums\QuoteStatus;
use Database\Factories\QuoteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property QuoteStatus $status
 * @property-read Organization $organization
 * @property-read Person $person
 * @property-read QuoteVersion|null $currentVersion
 * @property-read QuoteVersion|null $approvedVersion
 */
#[Fillable([
    'organization_id',
    'person_id',
    'number',
    'status',
    'current_version_id',
    'approved_version_id',
    'approved_at',
    'approved_by_organization_member_id',
])]
final class Quote extends Model
{
    /** @use HasFactory<QuoteFactory> */
    use HasFactory;

    protected static function newFactory(): QuoteFactory
    {
        return QuoteFactory::new();
    }

    protected function casts(): array
    {
        return [
            'status' => QuoteStatus::class,
            'approved_at' => 'immutable_datetime',
        ];
    }

    /** @return BelongsTo<Organization, $this> */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /** @return BelongsTo<Person, $this> */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /** @return HasMany<QuoteVersion, $this> */
    public function versions(): HasMany
    {
        return $this->hasMany(QuoteVersion::class)->orderBy('version_number');
    }

    /** @return BelongsTo<QuoteVersion, $this> */
    public function currentVersion(): BelongsTo
    {
        return $this->belongsTo(QuoteVersion::class, 'current_version_id');
    }

    /** @return BelongsTo<QuoteVersion, $this> */
    public function approvedVersion(): BelongsTo
    {
        return $this->belongsTo(QuoteVersion::class, 'approved_version_id');
    }

    /** @return BelongsTo<OrganizationMember, $this> */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(
            OrganizationMember::class,
            'approved_by_organization_member_id',
        );
    }
}
