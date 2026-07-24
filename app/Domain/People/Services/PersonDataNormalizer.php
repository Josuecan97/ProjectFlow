<?php

namespace App\Domain\People\Services;

use App\Domain\People\Enums\PersonType;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class PersonDataNormalizer
{
    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public function normalize(array $attributes): array
    {
        $type = PersonType::from($attributes['type']);
        $firstName = $this->nullableString($attributes['first_name'] ?? null);
        $lastName = $this->nullableString($attributes['last_name'] ?? null);
        $legalName = $this->nullableString($attributes['legal_name'] ?? null);
        $providedDisplayName = $this->nullableString($attributes['display_name'] ?? null);

        $displayName = $type === PersonType::Individual
            ? trim(implode(' ', array_filter([$firstName, $lastName])))
            : ($providedDisplayName ?? $legalName);

        return [
            'type' => $type,
            'display_name' => $displayName,
            'legal_name' => $type === PersonType::Organization ? $legalName : null,
            'first_name' => $type === PersonType::Individual ? $firstName : null,
            'last_name' => $type === PersonType::Individual ? $lastName : null,
            'tax_id' => $this->normalizedIdentifier($attributes['tax_id'] ?? null),
            'curp' => $type === PersonType::Individual
                ? $this->normalizedIdentifier($attributes['curp'] ?? null)
                : null,
            'primary_email' => $this->normalizedEmail($attributes['primary_email'] ?? null),
            'primary_phone' => $this->normalizedPhone($attributes['primary_phone'] ?? null),
            'website' => $this->nullableString($attributes['website'] ?? null),
            'address' => $this->normalizedAddress($attributes['address'] ?? null),
            'notes' => $this->nullableString($attributes['notes'] ?? null),
        ];
    }

    private function normalizedIdentifier(mixed $value): ?string
    {
        $value = $this->nullableString($value);

        return $value === null
            ? null
            : Str::upper(preg_replace('/[\s-]+/u', '', $value) ?? $value);
    }

    private function normalizedEmail(mixed $value): ?string
    {
        $value = $this->nullableString($value);

        return $value === null ? null : Str::lower($value);
    }

    private function normalizedPhone(mixed $value): ?string
    {
        $value = $this->nullableString($value);

        if ($value === null) {
            return null;
        }

        $hasInternationalPrefix = Str::startsWith($value, '+');
        $digits = preg_replace('/\D+/', '', $value);

        return $hasInternationalPrefix ? "+{$digits}" : $digits;
    }

    /**
     * @return array<string, string>|null
     */
    private function normalizedAddress(mixed $address): ?array
    {
        if (! is_array($address)) {
            return null;
        }

        $normalized = collect(Arr::only($address, [
            'street',
            'city',
            'state',
            'postal_code',
            'country',
        ]))
            ->map(fn (mixed $value): ?string => $this->nullableString($value))
            ->filter()
            ->all();

        if (isset($normalized['country'])) {
            $normalized['country'] = Str::upper($normalized['country']);
        }

        return $normalized === [] ? null : $normalized;
    }

    private function nullableString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
