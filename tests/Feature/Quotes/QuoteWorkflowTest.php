<?php

use App\Domain\Organizations\Actions\CreateOrganization;
use App\Domain\Organizations\Models\Organization;
use App\Domain\People\Enums\PersonRoleCode;
use App\Domain\People\Models\Person;
use App\Domain\People\Models\PersonRole;
use App\Domain\Quotes\Actions\ApproveQuote;
use App\Domain\Quotes\Actions\ArchiveQuote;
use App\Domain\Quotes\Actions\CorrectApprovedQuoteVersion;
use App\Domain\Quotes\Actions\CreateCommercialQuoteVersion;
use App\Domain\Quotes\Actions\CreateDraftQuote;
use App\Domain\Quotes\Actions\ExpireQuotes;
use App\Domain\Quotes\Actions\RejectQuote;
use App\Domain\Quotes\Actions\SendQuote;
use App\Domain\Quotes\Enums\QuoteStatus;
use App\Domain\Quotes\Enums\QuoteVersionStatus;
use App\Domain\Quotes\Events\QuoteApproved;
use App\Domain\Quotes\Events\QuoteExpired;
use App\Domain\Quotes\Models\Quote;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Database\Seeders\PersonRoleSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->seed([AccessControlSeeder::class, PersonRoleSeeder::class]);
});

it('sends and approves a quote with organization member traceability', function () {
    Event::fake([QuoteApproved::class]);
    [$owner, $organization, $quote] = workflowDraft();

    $sent = app(SendQuote::class)->handle($owner, $quote);
    $approved = app(ApproveQuote::class)->handle($owner, $sent);
    $member = $organization->members()->where('user_id', $owner->id)->sole();

    expect($approved->status)->toBe(QuoteStatus::Approved)
        ->and($approved->currentVersion->status)->toBe(QuoteVersionStatus::Approved)
        ->and($approved->approved_version_id)->toBe($approved->current_version_id)
        ->and($approved->approved_by_organization_member_id)->toBe($member->id)
        ->and($approved->approved_at)->not->toBeNull()
        ->and($approved->approvedVersion->approved_at)->not->toBeNull();

    Event::assertDispatched(
        QuoteApproved::class,
        fn (QuoteApproved $event): bool => $event->quoteId === $approved->id
            && $event->approvedByOrganizationMemberId === $member->id,
    );
});

it('requires the approved transition to start from sent', function () {
    [$owner, , $quote] = workflowDraft();

    expect(fn () => app(ApproveQuote::class)->handle($owner, $quote))
        ->toThrow(ValidationException::class);

    expect($quote->refresh()->status)->toBe(QuoteStatus::Draft);
});

it('creates a new commercial draft while preserving the approved agreement', function () {
    [$owner, , $quote, $client] = workflowDraft();
    $approved = app(ApproveQuote::class)->handle(
        $owner,
        app(SendQuote::class)->handle($owner, $quote),
    );
    $approvedVersionId = $approved->approved_version_id;
    $approvedTotal = $approved->approvedVersion->total;

    $revised = app(CreateCommercialQuoteVersion::class)->handle(
        $owner,
        $approved,
        workflowPayload($client, [
            'title' => 'Nuevo alcance comercial',
            'items' => [[
                'name' => 'Servicio ampliado',
                'quantity' => '3.0000',
                'unit' => 'servicio',
                'unit_price' => '200.000000',
                'discount_amount' => '0.000000',
                'tax_rate' => '16.0000',
            ]],
        ]),
    );

    expect($revised->status)->toBe(QuoteStatus::Draft)
        ->and($revised->versions)->toHaveCount(2)
        ->and($revised->currentVersion->version_number)->toBe(2)
        ->and($revised->currentVersion->status)->toBe(QuoteVersionStatus::Draft)
        ->and($revised->currentVersion->total)->toBe('696.000000')
        ->and($revised->approved_version_id)->toBe($approvedVersionId)
        ->and($revised->approvedVersion->total)->toBe($approvedTotal)
        ->and($revised->approvedVersion->status)->toBe(QuoteVersionStatus::Approved);
});

it('audits allowed administrative corrections without creating a version', function () {
    [$owner, , $quote] = workflowDraft();
    $approved = app(ApproveQuote::class)->handle(
        $owner,
        app(SendQuote::class)->handle($owner, $quote),
    );
    $version = $approved->approvedVersion;

    $corrected = app(CorrectApprovedQuoteVersion::class)->handle($owner, $version, [
        'contact_name' => 'Contacto corregido',
        'client_address' => ['country' => 'MX', 'city' => 'Mérida'],
    ]);

    expect($corrected->contact_name)->toBe('Contacto corregido')
        ->and($corrected->revisions)->toHaveCount(1)
        ->and($corrected->revisions->first()->before_values['contact_name'])->toBe('Ana Compras')
        ->and($corrected->revisions->first()->after_values['contact_name'])->toBe('Contacto corregido')
        ->and($approved->refresh()->versions)->toHaveCount(1);

    expect(fn () => app(CorrectApprovedQuoteVersion::class)->handle($owner, $corrected, [
        'title' => 'Cambio comercial encubierto',
    ]))->toThrow(ValidationException::class);
});

it('expires overdue active quotes once and emits a domain event', function () {
    Event::fake([QuoteExpired::class]);
    [, $organization, $quote] = workflowDraft([
        'issued_on' => now()->subDays(10)->toDateString(),
        'expires_on' => now()->subDay()->toDateString(),
    ]);

    $firstRun = app(ExpireQuotes::class)->handle($organization);
    $secondRun = app(ExpireQuotes::class)->handle($organization);

    expect($firstRun)->toBe(1)
        ->and($secondRun)->toBe(0)
        ->and($quote->refresh()->status)->toBe(QuoteStatus::Expired)
        ->and($quote->currentVersion->status)->toBe(QuoteVersionStatus::Expired);

    Event::assertDispatchedTimes(QuoteExpired::class, 1);
});

it('rejects sent quotes and archives terminal records without deleting history', function () {
    [$owner, , $quote] = workflowDraft();
    $rejected = app(RejectQuote::class)->handle(
        $owner,
        app(SendQuote::class)->handle($owner, $quote),
    );
    $archived = app(ArchiveQuote::class)->handle($owner, $rejected);

    expect($archived->status)->toBe(QuoteStatus::Archived)
        ->and($archived->versions)->toHaveCount(1)
        ->and($archived->currentVersion->status)->toBe(QuoteVersionStatus::Rejected);

    expect(fn () => app(ArchiveQuote::class)->handle($owner, $archived))
        ->toThrow(ValidationException::class);
});

/**
 * @param  array<string, mixed>  $overrides
 * @return array{User, Organization, Quote, Person}
 */
function workflowDraft(array $overrides = []): array
{
    $owner = User::factory()->create();
    $organization = app(CreateOrganization::class)->handle($owner, ['name' => 'Workflow']);
    $client = Person::factory()->for($organization)->create();
    $client->roles()->attach(
        PersonRole::query()->where('code', PersonRoleCode::Client->value)->sole(),
    );
    $quote = app(CreateDraftQuote::class)->handle(
        $owner,
        $organization,
        workflowPayload($client, $overrides),
    );

    return [$owner, $organization, $quote, $client];
}

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function workflowPayload(Person $person, array $overrides = []): array
{
    return array_replace([
        'person_id' => $person->id,
        'title' => 'Cotización de prueba',
        'description' => 'Propuesta comercial',
        'scope' => 'Alcance acordado',
        'terms' => 'Pago a 15 días',
        'notes' => 'Observaciones',
        'issued_on' => now()->toDateString(),
        'expires_on' => now()->addDays(14)->toDateString(),
        'currency' => 'MXN',
        'client_name' => $person->display_name,
        'contact_name' => 'Ana Compras',
        'contact_email' => 'ana@example.test',
        'contact_phone' => '9991234567',
        'client_address' => ['country' => 'MX'],
        'items' => [[
            'name' => 'Servicio',
            'quantity' => '1.0000',
            'unit' => 'servicio',
            'unit_price' => '100.000000',
            'discount_amount' => '0.000000',
            'tax_rate' => '16.0000',
        ]],
    ], $overrides);
}
