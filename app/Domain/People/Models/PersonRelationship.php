<?php

namespace App\Domain\People\Models;

use App\Domain\Organizations\Models\Organization;
use App\Domain\People\Enums\PersonRelationshipType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'organization_id',
    'parent_person_id',
    'related_person_id',
    'type',
    'job_title',
    'is_primary',
    'notes',
])]
final class PersonRelationship extends Model
{
    protected function casts(): array
    {
        return [
            'type' => PersonRelationshipType::class,
            'is_primary' => 'boolean',
        ];
    }

    /** @return BelongsTo<Organization, $this> */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /** @return BelongsTo<Person, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'parent_person_id')->withTrashed();
    }

    /** @return BelongsTo<Person, $this> */
    public function related(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'related_person_id')->withTrashed();
    }
}
