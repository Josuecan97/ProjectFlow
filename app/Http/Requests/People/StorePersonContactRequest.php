<?php

namespace App\Http\Requests\People;

use App\Domain\Organizations\Support\CurrentOrganization;
use App\Domain\People\Models\Person;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StorePersonContactRequest extends FormRequest
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
        return self::rulesFor(app(CurrentOrganization::class)->id());
    }

    /**
     * @return array<string, mixed>
     */
    public static function rulesFor(int $organizationId): array
    {
        return [
            'related_person_id' => [
                'required',
                'integer',
                Rule::exists('people', 'id')
                    ->where('organization_id', $organizationId)
                    ->whereNull('deleted_at'),
            ],
            'job_title' => ['nullable', 'string', 'max:255'],
            'is_primary' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
