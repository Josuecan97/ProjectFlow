<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Support;

use App\Domain\People\Enums\PersonStatus;
use Illuminate\Validation\Rule;

final class QuoteDraftRules
{
    /**
     * @return array<string, mixed>
     */
    public static function forOrganization(int $organizationId): array
    {
        return [
            'person_id' => [
                'required',
                'integer',
                Rule::exists('people', 'id')
                    ->where('organization_id', $organizationId)
                    ->where('status', PersonStatus::Active->value)
                    ->whereNull('deleted_at'),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'scope' => ['nullable', 'string', 'max:20000'],
            'terms' => ['nullable', 'string', 'max:20000'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'issued_on' => ['required', 'date_format:Y-m-d'],
            'expires_on' => ['required', 'date_format:Y-m-d', 'after_or_equal:issued_on'],
            'currency' => ['required', 'string', 'size:3', 'alpha:ascii'],
            'client_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email:rfc', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'client_address' => ['nullable', 'array'],
            'client_address.street' => ['nullable', 'string', 'max:255'],
            'client_address.city' => ['nullable', 'string', 'max:120'],
            'client_address.state' => ['nullable', 'string', 'max:120'],
            'client_address.postal_code' => ['nullable', 'string', 'max:12'],
            'client_address.country' => ['nullable', 'string', 'size:2', 'alpha:ascii'],
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string', 'max:5000'],
            'items.*.quantity' => ['required', 'decimal:0,4', 'gt:0', 'max:999999999999'],
            'items.*.unit' => ['required', 'string', 'max:40'],
            'items.*.unit_price' => ['required', 'decimal:0,6', 'gte:0', 'max:99999999999999'],
            'items.*.discount_amount' => ['nullable', 'decimal:0,6', 'gte:0', 'max:99999999999999'],
            'items.*.tax_rate' => ['nullable', 'decimal:0,4', 'between:0,100'],
        ];
    }
}
