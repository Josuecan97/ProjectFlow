<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Services;

use App\Domain\Quotes\Models\QuoteVersion;

final class SaveQuoteDraft
{
    public function __construct(private readonly QuoteCalculator $calculator) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(QuoteVersion $version, array $attributes): QuoteVersion
    {
        /** @var array<int, array<string, mixed>> $items */
        $items = $attributes['items'];
        $totals = $this->calculator->calculate($items);

        $version->fill([
            'title' => $attributes['title'],
            'description' => $attributes['description'] ?? null,
            'scope' => $attributes['scope'] ?? null,
            'terms' => $attributes['terms'] ?? null,
            'notes' => $attributes['notes'] ?? null,
            'issued_on' => $attributes['issued_on'],
            'expires_on' => $attributes['expires_on'],
            'currency' => $attributes['currency'],
            'client_name' => $attributes['client_name'],
            'contact_name' => $attributes['contact_name'] ?? null,
            'contact_email' => $attributes['contact_email'] ?? null,
            'contact_phone' => $attributes['contact_phone'] ?? null,
            'client_address' => $attributes['client_address'] ?? null,
            'subtotal' => $totals->subtotal,
            'discount_total' => $totals->discountTotal,
            'tax_total' => $totals->taxTotal,
            'total' => $totals->total,
        ])->save();

        $version->items()->delete();

        foreach (array_values($items) as $index => $item) {
            $amounts = $this->calculator->calculateItem($item);

            $version->items()->create([
                'organization_id' => $version->organization_id,
                'position' => $index + 1,
                'name' => $item['name'],
                'description' => $item['description'] ?? null,
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'unit_price' => $item['unit_price'],
                'discount_amount' => $item['discount_amount'] ?? '0',
                'tax_rate' => $item['tax_rate'] ?? '0',
                'subtotal' => $amounts->subtotal,
                'tax_amount' => $amounts->taxAmount,
                'total' => $amounts->total,
            ]);
        }

        return $version->refresh()->load('items');
    }
}
