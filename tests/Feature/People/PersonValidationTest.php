<?php

use App\Domain\Organizations\Models\Organization;
use App\Domain\People\Models\Person;
use App\Http\Requests\People\StorePersonRequest;
use App\Http\Requests\People\UpdatePersonRequest;
use Illuminate\Support\Facades\Validator;

it('requires names according to the person type', function (string $type, string $missingField) {
    $organization = Organization::factory()->create();
    $validator = Validator::make(
        ['type' => $type],
        StorePersonRequest::rulesFor($organization->id),
    );

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has($missingField))->toBeTrue();
})->with([
    'physical person' => ['individual', 'first_name'],
    'moral person' => ['organization', 'legal_name'],
]);

it('allows a person to retain their own tax id during update', function () {
    $person = Person::factory()->create(['tax_id' => 'RFC010101AAA']);
    $validator = Validator::make([
        'type' => 'individual',
        'first_name' => 'Ana',
        'tax_id' => 'RFC010101AAA',
    ], UpdatePersonRequest::rulesFor($person->organization_id, $person->id));

    expect($validator->passes())->toBeTrue();
});

it('validates country codes and rejects duplicated commercial roles', function () {
    $organization = Organization::factory()->create();
    $validator = Validator::make([
        'type' => 'individual',
        'first_name' => 'Ana',
        'address' => ['country' => 'MEX'],
        'role_ids' => [1, 1],
    ], StorePersonRequest::rulesFor($organization->id));

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('address.country'))->toBeTrue()
        ->and($validator->errors()->has('role_ids.0'))->toBeTrue();
});
