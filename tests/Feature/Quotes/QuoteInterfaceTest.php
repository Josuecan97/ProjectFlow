<?php

use App\Domain\Organizations\Actions\CreateOrganization;
use App\Domain\Organizations\Models\Organization;
use App\Domain\Organizations\Support\CurrentOrganization;
use App\Domain\People\Enums\PersonRoleCode;
use App\Domain\People\Models\Person;
use App\Domain\People\Models\PersonRole;
use App\Domain\Quotes\Actions\ApproveQuote;
use App\Domain\Quotes\Actions\CreateDraftQuote;
use App\Domain\Quotes\Actions\SendQuote;
use App\Domain\Quotes\Enums\QuoteStatus;
use App\Domain\Quotes\Models\Quote;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Database\Seeders\PersonRoleSeeder;
use Livewire\Volt\Volt;

beforeEach(function () {
    $this->seed([AccessControlSeeder::class, PersonRoleSeeder::class]);
});

it('renders every quotation screen for an authorized owner', function () {
    [$owner, $organization, $client, $quote] = quotationInterfaceContext();
    $this->actingAs($owner)->withSession(['organization_id' => $organization->id]);

    foreach ([
        route('quotes.index'),
        route('quotes.create'),
        route('quotes.show', $quote),
        route('quotes.edit', $quote),
    ] as $url) {
        $this->get($url)->assertOk();
    }

    expect($client->organization_id)->toBe($organization->id);
});

it('creates and edits a quotation from the unified Volt form', function () {
    [$owner, $organization, $client] = quotationInterfaceContext(createQuote: false);
    $this->actingAs($owner);
    app(CurrentOrganization::class)->set($organization);

    $component = Volt::test('quotes.form')
        ->set('person_id', (string) $client->id)
        ->set('title', 'Propuesta desde UI')
        ->set('issued_on', now()->toDateString())
        ->set('expires_on', now()->addDays(14)->toDateString())
        ->set('currency', 'MXN')
        ->set('client_name', $client->display_name)
        ->set('items', [[
            'name' => 'Servicio UI',
            'description' => '',
            'quantity' => '2.0000',
            'unit' => 'servicio',
            'unit_price' => '50.000000',
            'discount_amount' => '0.000000',
            'tax_rate' => '16.0000',
        ]])
        ->call('save')
        ->assertHasNoErrors();

    $quote = Quote::query()->sole();

    expect($quote->currentVersion->title)->toBe('Propuesta desde UI')
        ->and($quote->currentVersion->total)->toBe('116.000000');

    Volt::test('quotes.form', ['quote' => $quote])
        ->set('title', 'Propuesta editada')
        ->call('save')
        ->assertHasNoErrors();

    expect($quote->currentVersion->fresh()->title)->toBe('Propuesta editada');
    $component->assertRedirect(route('quotes.show', $quote, absolute: false));
});

it('executes the sent transition from the quotation record', function () {
    [$owner, $organization, , $quote] = quotationInterfaceContext();
    $this->actingAs($owner);
    app(CurrentOrganization::class)->set($organization);

    Volt::test('quotes.show', ['quote' => $quote])
        ->call('send')
        ->assertHasNoErrors()
        ->assertSee('Enviada');

    expect($quote->fresh()->status)->toBe(QuoteStatus::Sent);
});

it('renders commercial revision and administrative audit controls after approval', function () {
    [$owner, $organization, , $quote] = quotationInterfaceContext();
    $approved = app(ApproveQuote::class)->handle(
        $owner,
        app(SendQuote::class)->handle($owner, $quote),
    );
    $this->actingAs($owner)->withSession(['organization_id' => $organization->id]);

    $this->get(route('quotes.revise', $approved))
        ->assertOk()
        ->assertSee('Nueva versión comercial');
    $this->get(route('quotes.show', $approved))
        ->assertOk()
        ->assertSee('Corrección administrativa')
        ->assertSee('Nueva versión');
});

it('returns not found for quotation records from another selected organization', function () {
    [$owner, $selected] = quotationInterfaceContext(createQuote: false);
    $other = app(CreateOrganization::class)->handle($owner, ['name' => 'Otra']);
    $client = interfaceClient($other);
    $quote = app(CreateDraftQuote::class)->handle(
        $owner,
        $other,
        interfaceQuotePayload($client),
    );

    $this->actingAs($owner)
        ->withSession(['organization_id' => $selected->id])
        ->get(route('quotes.show', $quote))
        ->assertNotFound();
});

it('keeps quotations readable but blocks mutation screens in read only mode', function () {
    [$owner, $organization, , $quote] = quotationInterfaceContext();
    $organization->currentSubscription->update(['ends_at' => now()->subMinute()]);
    $organization->unsetRelation('currentSubscription');
    $this->actingAs($owner)->withSession(['organization_id' => $organization->id]);

    $this->get(route('quotes.index'))->assertOk()->assertDontSee('Nueva Cotización');
    $this->get(route('quotes.show', $quote))->assertOk()->assertDontSee('Editar Draft');
    $this->get(route('quotes.create'))->assertForbidden();
    $this->get(route('quotes.edit', $quote))->assertForbidden();
});

/**
 * @return array{User, Organization, Person, Quote|null}
 */
function quotationInterfaceContext(bool $createQuote = true): array
{
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Interfaz']);
    $client = interfaceClient($organization);
    $quote = $createQuote
        ? app(CreateDraftQuote::class)->handle(
            $owner,
            $organization,
            interfaceQuotePayload($client),
        )
        : null;

    return [$owner, $organization, $client, $quote];
}

function interfaceClient(Organization $organization): Person
{
    $person = Person::factory()->for($organization)->create();
    $person->roles()->attach(
        PersonRole::query()->where('code', PersonRoleCode::Client->value)->sole(),
    );

    return $person;
}

/**
 * @return array<string, mixed>
 */
function interfaceQuotePayload(Person $person): array
{
    return [
        'person_id' => $person->id,
        'title' => 'Cotización UI',
        'issued_on' => now()->toDateString(),
        'expires_on' => now()->addDays(14)->toDateString(),
        'currency' => 'MXN',
        'client_name' => $person->display_name,
        'items' => [[
            'name' => 'Servicio',
            'quantity' => '1.0000',
            'unit' => 'servicio',
            'unit_price' => '100.000000',
            'discount_amount' => '0.000000',
            'tax_rate' => '16.0000',
        ]],
    ];
}
