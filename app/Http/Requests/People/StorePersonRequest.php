<?php

namespace App\Http\Requests\People;

use App\Domain\Organizations\Support\CurrentOrganization;
use App\Domain\People\Enums\PersonRoleCode;
use App\Domain\People\Enums\PersonType;
use App\Domain\People\Models\Person;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StorePersonRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $organization = app(CurrentOrganization::class)->get();

        return $user?->can('create', [Person::class, $organization]) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return self::rulesFor(app(CurrentOrganization::class)->id());
    }

    /**
     * Rules are reused by Volt while organization_id is always supplied by server context.
     *
     * @return array<string, mixed>
     */
    public static function rulesFor(int $organizationId): array
    {
        return [
            'type' => ['required', Rule::enum(PersonType::class)],
            'display_name' => ['nullable', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255', 'required_if:type,organization'],
            'first_name' => ['nullable', 'string', 'max:120', 'required_if:type,individual'],
            'last_name' => ['nullable', 'string', 'max:120'],
            'tax_id' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('people', 'tax_id')->where('organization_id', $organizationId),
            ],
            'curp' => ['nullable', 'string', 'size:18'],
            'primary_email' => ['nullable', 'email:rfc', 'max:255'],
            'primary_phone' => ['nullable', 'string', 'max:30'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
            'address' => ['nullable', 'array'],
            'address.street' => ['nullable', 'string', 'max:255'],
            'address.city' => ['nullable', 'string', 'max:120'],
            'address.state' => ['nullable', 'string', 'max:120'],
            'address.postal_code' => ['nullable', 'string', 'max:12'],
            'address.country' => ['nullable', 'string', 'size:2', 'alpha'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'role_ids' => ['nullable', 'array', 'max:'.count(PersonRoleCode::cases())],
            'role_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('person_roles', 'id')->whereIn(
                    'code',
                    array_column(PersonRoleCode::cases(), 'value'),
                ),
            ],
        ];
    }
}
