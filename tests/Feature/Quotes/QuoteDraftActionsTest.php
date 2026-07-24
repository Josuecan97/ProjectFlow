<?php

use App\Domain\Organizations\Actions\CreateOrganization;
use App\Domain\Organizations\Models\Organization;
use App\Domain\People\Enums\PersonRoleCode;
use App\Domain\People\Models\Person;
use App\Domain\People\Models\PersonRole;
use App\Domain\Quotes\Actions\CreateDraftQuote;
use App\Domain\Quotes\Actions\UpdateDraftQuote;
use App\Domain\Quotes\Enums\QuoteStatus;
use App\Domain\Quotes\Enums\QuoteVersionStatus;
use App\Domain\Quotes\Services\QuoteCalculator;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Database\Seeders\PersonRoleSeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->seed([AccessControlSeeder::class, PersonRoleSeeder::class]);
});

it('calculates exact internal amounts and rounds display values half up', function () {
    $calculator = app(QuoteCalculator::class);
    $amounts = $calculator->calculateItem([
        'quantity' => '2.5000',
        'unit_price' => '19.999999',
        'discount_amount' => '0.123456',
        'tax_rate' => '16.0000',
    ]);

    expect($amounts->subtotal)->toBe('49.876542')
        ->and($amounts->taxAmount)->toBe('7.980247')
        ->and($amounts->total)->toBe('57.856789')
        ->and($calculator->display('57.855000'))->toBe('57.86');
});

it('rejects discounts greater than the base amount', function () {
    expect(fn () => app(QuoteCalculator::class)->calculateItem([
        'quantity' => '2',
        'unit_price' => '10',
        'discount_amount' => '20.000001',
        'tax_rate' => '16',
    ]))->toThrow(ValidationException::class);
});

it('creates a draft aggregate with a transactional tenant sequence', function () {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Acme']);
    $client = clientFor($organization);

    $first = app(CreateDraftQuote::class)->handle(
        $owner,
        $organization,
        quotePayload($client),
    );
    $second = app(CreateDraftQuote::class)->handle(
        $owner,
        $organization,
        quotePayload($client, ['title' => 'Segunda cotización']),
    );

    expect($first->number)->toBe('COT-000001')
        ->and($second->number)->toBe('COT-000002')
        ->and($first->status)->toBe(QuoteStatus::Draft)
        ->and($first->currentVersion->status)->toBe(QuoteVersionStatus::Draft)
        ->and($first->currentVersion->version_number)->toBe(1)
        ->and($first->currentVersion->subtotal)->toBe('180.000000')
        ->and($first->currentVersion->discount_total)->toBe('20.000000')
        ->and($first->currentVersion->tax_total)->toBe('28.800000')
        ->and($first->currentVersion->total)->toBe('208.800000')
        ->and($first->currentVersion->items)->toHaveCount(1)
        ->and($first->currentVersion->created_by_organization_member_id)
        ->toBe($organization->members()->where('user_id', $owner->id)->sole()->id);
});

it('starts an independent sequence for every organization', function () {
    $firstOwner = User::factory()->create();
    $secondOwner = User::factory()->create();
    $firstOrganization = app(CreateOrganization::class)->handle($firstOwner, ['name' => 'Uno']);
    $secondOrganization = app(CreateOrganization::class)->handle($secondOwner, ['name' => 'Dos']);

    $first = app(CreateDraftQuote::class)->handle(
        $firstOwner,
        $firstOrganization,
        quotePayload(clientFor($firstOrganization)),
    );
    $second = app(CreateDraftQuote::class)->handle(
        $secondOwner,
        $secondOrganization,
        quotePayload(clientFor($secondOrganization)),
    );

    expect($first->number)->toBe('COT-000001')
        ->and($second->number)->toBe('COT-000001');
});

it('requires the client role without assigning it silently', function () {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Acme']);
    $person = Person::factory()->for($organization)->create();

    expect(fn () => app(CreateDraftQuote::class)->handle(
        $owner,
        $organization,
        quotePayload($person),
    ))->toThrow(ValidationException::class);

    expect($person->roles()->count())->toBe(0)
        ->and($organization->quotes()->count())->toBe(0);
});

it('edits the same draft version and recalculates server totals', function () {
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Acme']);
    $client = clientFor($organization);
    $quote = app(CreateDraftQuote::class)->handle(
        $owner,
        $organization,
        quotePayload($client),
    );
    $versionId = $quote->current_version_id;

    $updated = app(UpdateDraftQuote::class)->handle(
        $owner,
        $quote,
        quotePayload($client, [
            'title' => 'Alcance ajustado',
            'items' => [[
                'name' => 'Implementación',
                'quantity' => '3.0000',
                'unit' => 'servicio',
                'unit_price' => '100.000000',
                'discount_amount' => '0.000000',
                'tax_rate' => '0.0000',
            ]],
        ]),
    );

    expect($updated->current_version_id)->toBe($versionId)
        ->and($updated->versions)->toHaveCount(1)
        ->and($updated->currentVersion->title)->toBe('Alcance ajustado')
        ->and($updated->currentVersion->subtotal)->toBe('300.000000')
        ->and($updated->currentVersion->total)->toBe('300.000000')
        ->and($updated->currentVersion->items)->toHaveCount(1);
});

it('denies cross-tenant updates and write access without an active subscription', function () {
    $owner = User::factory()->create();
    $outsider = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Acme']);
    app(CreateOrganization::class)->handle($outsider, ['name' => 'Otra']);
    $client = clientFor($organization);
    $quote = app(CreateDraftQuote::class)->handle(
        $owner,
        $organization,
        quotePayload($client),
    );

    expect(fn () => app(UpdateDraftQuote::class)->handle(
        $outsider,
        $quote,
        quotePayload($client),
    ))->toThrow(AuthorizationException::class);

    $organization->currentSubscription->update(['ends_at' => now()->subMinute()]);
    $organization->unsetRelation('currentSubscription');

    expect(fn () => app(UpdateDraftQuote::class)->handle(
        $owner,
        $quote,
        quotePayload($client),
    ))->toThrow(AuthorizationException::class);
});

function clientFor(Organization $organization): Person
{
    $person = Person::factory()->for($organization)->create();
    $person->roles()->attach(
        PersonRole::query()->where('code', PersonRoleCode::Client->value)->sole(),
    );

    return $person;
}

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function quotePayload(Person $person, array $overrides = []): array
{
    return array_replace([
        'person_id' => $person->id,
        'title' => 'Implementación ProjectFlow',
        'description' => 'Propuesta comercial',
        'scope' => 'Configuración inicial',
        'terms' => 'Pago a 15 días',
        'notes' => 'Atención prioritaria',
        'issued_on' => now()->toDateString(),
        'expires_on' => now()->addDays(15)->toDateString(),
        'currency' => 'mxn',
        'client_name' => $person->display_name,
        'contact_name' => 'Ana Compras',
        'contact_email' => 'ana@example.test',
        'contact_phone' => '9991234567',
        'client_address' => ['country' => 'MX'],
        'items' => [[
            'name' => 'Implementación',
            'quantity' => '2.0000',
            'unit' => 'servicio',
            'unit_price' => '100.000000',
            'discount_amount' => '20.000000',
            'tax_rate' => '16.0000',
        ]],
    ], $overrides);
}
