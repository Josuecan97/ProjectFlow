<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Models;

use App\Domain\Organizations\Models\Organization;
use App\Domain\Organizations\Models\OrganizationMember;
use App\Domain\Quotes\Enums\QuoteVersionStatus;
use Database\Factories\QuoteVersionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property QuoteVersionStatus $status
 * @property-read Organization $organization
 * @property-read Quote $quote
 */
#[Fillable([
    'organization_id',
    'quote_id',
    'version_number',
    'status',
    'title',
    'description',
    'scope',
    'terms',
    'notes',
    'issued_on',
    'expires_on',
    'currency',
    'client_name',
    'contact_name',
    'contact_email',
    'contact_phone',
    'client_address',
    'subtotal',
    'discount_total',
    'tax_total',
    'total',
    'created_by_organization_member_id',
    'approved_at',
])]
final class QuoteVersion extends Model
{
    /** @use HasFactory<QuoteVersionFactory> */
    use HasFactory;

    protected static function newFactory(): QuoteVersionFactory
    {
        return QuoteVersionFactory::new();
    }

    protected function casts(): array
    {
        return [
            'status' => QuoteVersionStatus::class,
            'issued_on' => 'immutable_date',
            'expires_on' => 'immutable_date',
            'client_address' => 'array',
            'subtotal' => 'decimal:6',
            'discount_total' => 'decimal:6',
            'tax_total' => 'decimal:6',
            'total' => 'decimal:6',
            'approved_at' => 'immutable_datetime',
        ];
    }

    /** @return BelongsTo<Organization, $this> */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /** @return BelongsTo<Quote, $this> */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /** @return BelongsTo<OrganizationMember, $this> */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(
            OrganizationMember::class,
            'created_by_organization_member_id',
        );
    }

    /** @return HasMany<QuoteItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class)->orderBy('position');
    }

    /** @return HasMany<QuoteVersionRevision, $this> */
    public function revisions(): HasMany
    {
        return $this->hasMany(QuoteVersionRevision::class)->orderByDesc('created_at');
    }
}
