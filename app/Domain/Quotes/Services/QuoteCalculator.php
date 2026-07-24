<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Services;

use App\Domain\Quotes\ValueObjects\QuoteItemAmounts;
use App\Domain\Quotes\ValueObjects\QuoteTotals;
use Brick\Math\BigDecimal;
use Brick\Math\Exception\MathException;
use Brick\Math\RoundingMode;
use Illuminate\Validation\ValidationException;

final class QuoteCalculator
{
    private const int INTERNAL_SCALE = 6;

    /**
     * @param  array<string, mixed>  $item
     */
    public function calculateItem(array $item): QuoteItemAmounts
    {
        try {
            $quantity = BigDecimal::of((string) $item['quantity']);
            $unitPrice = BigDecimal::of((string) $item['unit_price']);
            $discount = BigDecimal::of((string) ($item['discount_amount'] ?? '0'));
            $taxRate = BigDecimal::of((string) ($item['tax_rate'] ?? '0'));
        } catch (MathException|\InvalidArgumentException) {
            throw ValidationException::withMessages([
                'items' => __('Los valores monetarios deben ser números decimales válidos.'),
            ]);
        }

        if ($quantity->isLessThanOrEqualTo(0)) {
            throw ValidationException::withMessages([
                'items' => __('La cantidad debe ser mayor que cero.'),
            ]);
        }

        if ($unitPrice->isNegative() || $discount->isNegative()) {
            throw ValidationException::withMessages([
                'items' => __('El precio y el descuento no pueden ser negativos.'),
            ]);
        }

        if ($taxRate->isNegative() || $taxRate->isGreaterThan(100)) {
            throw ValidationException::withMessages([
                'items' => __('La tasa de impuesto debe estar entre 0 y 100.'),
            ]);
        }

        $base = $quantity->multipliedBy($unitPrice);

        if ($discount->isGreaterThan($base)) {
            throw ValidationException::withMessages([
                'items' => __('El descuento no puede superar el importe base.'),
            ]);
        }

        $subtotal = $base->minus($discount);
        $taxAmount = $subtotal->multipliedBy($taxRate)->dividedBy(
            100,
            self::INTERNAL_SCALE,
            RoundingMode::HalfUp,
        );
        $total = $subtotal->plus($taxAmount);

        return new QuoteItemAmounts(
            $this->internal($subtotal),
            $this->internal($taxAmount),
            $this->internal($total),
        );
    }

    /**
     * @param  iterable<array<string, mixed>>  $items
     */
    public function calculate(iterable $items): QuoteTotals
    {
        $subtotal = BigDecimal::zero();
        $discountTotal = BigDecimal::zero();
        $taxTotal = BigDecimal::zero();
        $total = BigDecimal::zero();

        foreach ($items as $item) {
            $amounts = $this->calculateItem($item);
            $subtotal = $subtotal->plus($amounts->subtotal);
            $discountTotal = $discountTotal->plus((string) ($item['discount_amount'] ?? '0'));
            $taxTotal = $taxTotal->plus($amounts->taxAmount);
            $total = $total->plus($amounts->total);
        }

        return new QuoteTotals(
            $this->internal($subtotal),
            $this->internal($discountTotal),
            $this->internal($taxTotal),
            $this->internal($total),
        );
    }

    public function display(string $amount): string
    {
        return BigDecimal::of($amount)->toScale(2, RoundingMode::HalfUp)->__toString();
    }

    private function internal(BigDecimal $amount): string
    {
        return $amount->toScale(self::INTERNAL_SCALE, RoundingMode::HalfUp)->__toString();
    }
}
