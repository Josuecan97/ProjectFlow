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
            'notes' => ['sometimes', 'nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function fields(): array
    {
        return array_keys(self::rules());
    }
}
