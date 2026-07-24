<?php

use App\Domain\Organizations\Actions\CreateOrganization;
use App\Domain\Organizations\Models\Organization;
use App\Domain\People\Enums\PersonRoleCode;
use App\Domain\People\Models\Person;
use App\Domain\People\Models\PersonRole;
use App\Domain\Quotes\Actions\CreateDraftQuote;
use App\Domain\Quotes\Models\Quote;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Database\Seeders\PersonRoleSeeder;

beforeEach(function () {
    $this->seed([AccessControlSeeder::class, PersonRoleSeeder::class]);
});

it('generates an authorized quotation PDF under demand', function () {
    [$owner, $organization, $quote] = pdfQuoteContext();

    $response = $this->actingAs($owner)
        ->withSession(['organization_id' => $organization->id])
        ->get(route('quotes.versions.pdf', [$quote, $quote->currentVersion]));

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/pdf')
        ->assertHeader('X-Content-Type-Options', 'nosniff');

    expect($response->headers->get('Content-Disposition'))
        ->toContain('COT-000001-v1.pdf')
        ->and($response->getContent())->toStartWith('%PDF-');
});

it('does not expose a PDF across organizations', function () {
    [$owner, $selected] = pdfQuoteContext();
    $other = app(CreateOrganization::class)->handle($owner, ['name' => 'Otra']);
    $quote = pdfQuoteFor($owner, $other);

    $this->actingAs($owner)
        ->withSession(['organization_id' => $selected->id])
        ->get(route('quotes.versions.pdf', [$quote, $quote->currentVersion]))
        ->assertNotFound();
});

it('rejects a version that does not belong to the requested quotation', function () {
    [$owner, $organization, $quote] = pdfQuoteContext();
    $otherQuote = pdfQuoteFor($owner, $organization);

    $this->actingAs($owner)
        ->withSession(['organization_id' => $organization->id])
        ->get(route('quotes.versions.pdf', [$quote, $otherQuote->currentVersion]))
        ->assertNotFound();
});

/**
 * @return array{User, Organization, Quote}
 */
function pdfQuoteContext(): array
{
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Estudio Norte']);

    return [$owner, $organization, pdfQuoteFor($owner, $organization)];
}

function pdfQuoteFor(User $owner, Organization $organization): Quote
{
    $client = Person::factory()->for($organization)->create(['display_name' => 'Cliente PDF']);
    $client->roles()->attach(
        PersonRole::query()->where('code', PersonRoleCode::Client->value)->sole(),
    );

    return app(CreateDraftQuote::class)->handle($owner, $organization, [
        'person_id' => $client->id,
        'title' => 'Implementación de plataforma',
        'description' => 'Propuesta preparada para validar el documento.',
        'scope' => 'Configuración y capacitación.',
        'terms' => 'Pago dentro de 15 días.',
        'notes' => 'Importes expresados en moneda nacional.',
        'issued_on' => now()->toDateString(),
        'expires_on' => now()->addDays(14)->toDateString(),
        'currency' => 'MXN',
        'client_name' => $client->display_name,
        'contact_name' => 'Ana Compras',
        'contact_email' => 'ana@example.test',
        'client_address' => ['city' => 'Mérida', 'country' => 'MX'],
        'items' => [[
            'name' => 'Implementación',
            'description' => 'Configuración inicial',
            'quantity' => '2.0000',
            'unit' => 'servicio',
            'unit_price' => '1000.000000',
            'discount_amount' => '100.000000',
            'tax_rate' => '16.0000',
        ]],
    ]);
}
