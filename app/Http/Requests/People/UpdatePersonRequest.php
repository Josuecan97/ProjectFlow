<?php

namespace App\Http\Requests\People;

use App\Domain\Organizations\Support\CurrentOrganization;
use App\Domain\People\Models\Person;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdatePersonRequest extends FormRequest
{
    public function authorize(): bool
    {
        $person = $this->route('person');

        return $person instanceof Person
            && ($this->user()?->can('update', $person) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $person = $this->route('person');

        return self::rulesFor(
            app(CurrentOrganization::class)->id(),
            $person instanceof Person ? $person->id : (int) $person,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function rulesFor(int $organizationId, int $personId): array
    {
        $rules = StorePersonRequest::rulesFor($organizationId);
        $rules['tax_id'] = [
            'nullable',
            'string',
            'max:20',
            Rule::unique('people', 'tax_id')
                ->where('organization_id', $organizationId)
                ->ignore($personId),
        ];

        return $rules;
    }
}
