<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Models;

use App\Domain\Organizations\Models\Organization;
use Database\Factories\QuoteItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'organization_id',
    'quote_version_id',
    'position',
    'name',
    'description',
    'quantity',
    'unit',
    'unit_price',
    'discount_amount',
    'tax_rate',
    'subtotal',
    'tax_amount',
    'total',
])]
final class QuoteItem extends Model
{
    /** @use HasFactory<QuoteItemFactory> */
    use HasFactory;

    protected static function newFactory(): QuoteItemFactory
    {
        return QuoteItemFactory::new();
    }

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:4',
            'unit_price' => 'decimal:6',
            'discount_amount' => 'decimal:6',
            'tax_rate' => 'decimal:4',
            'subtotal' => 'decimal:6',
            'tax_amount' => 'decimal:6',
            'total' => 'decimal:6',
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
}
