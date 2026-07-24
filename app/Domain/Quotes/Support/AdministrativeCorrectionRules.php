<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Support;

final class AdministrativeCorrectionRules
{
    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'client_name' => ['sometimes', 'required', 'string', 'max:255'],
            'contact_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'contact_email' => ['sometimes', 'nullable', 'email:rfc', 'max:255'],
            'contact_phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'client_address' => ['sometimes', 'nullable', 'array'],
            'client_address.street' => ['nullable', 'string', 'max:255'],
            'client_address.city' => ['nullable', 'string', 'max:120'],
            'client_address.state' => ['nullable', 'string', 'max:120'],
            'client_address.postal_code' => ['nullable', 'string', 'max:12'],
            'client_address.country' => ['nullable', 'string', 'size:2', 'alpha:ascii'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function fields(): array
    {
        return [
            'client_name',
            'contact_name',
            'contact_email',
            'contact_phone',
            'client_address',
            'notes',
        ];
    }
}
