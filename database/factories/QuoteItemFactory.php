<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Quotes\Models\QuoteItem;
use App\Domain\Quotes\Models\QuoteVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuoteItem>
 */
final class QuoteItemFactory extends Factory
{
    protected $model = QuoteItem::class;

    public function definition(): array
    {
        return [
            'quote_version_id' => QuoteVersion::factory(),
            'organization_id' => fn (array $attributes): int => QuoteVersion::query()
                ->findOrFail($attributes['quote_version_id'])
                ->organization_id,
            'position' => 1,
            'name' => fake()->sentence(3),
            'description' => fake()->optional()->sentence(),
            'quantity' => '1.0000',
            'unit' => 'servicio',
            'unit_price' => '1000.000000',
            'discount_amount' => '0.000000',
            'tax_rate' => '16.0000',
            'subtotal' => '1000.000000',
            'tax_amount' => '160.000000',
            'total' => '1160.000000',
        ];
    }
}
