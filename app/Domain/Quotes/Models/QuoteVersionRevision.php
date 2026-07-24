<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Models;

use App\Domain\Organizations\Models\Organization;
use App\Domain\Organizations\Models\OrganizationMember;
use App\Domain\Quotes\Enums\QuoteVersionRevisionType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'organization_id',
    'quote_version_id',
    'changed_by_organization_member_id',
    'type',
    'before_values',
    'after_values',
])]
final class QuoteVersionRevision extends Model
{
    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'type' => QuoteVersionRevisionType::class,
            'before_values' => 'array',
            'after_values' => 'array',
            'created_at' => 'immutable_datetime',
        ];
    }

    /** @return BelongsTo<Organization, $this> */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /** @return BelongsTo<QuoteVersion, $this> */
    public function version(): BelongsTo
    {
        return $this->belongsTo(QuoteVersion::class, 'quote_version_id');
    }

    /** @return BelongsTo<OrganizationMember, $this> */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(
            OrganizationMember::class,
            'changed_by_organization_member_id',
        );
    }
}
