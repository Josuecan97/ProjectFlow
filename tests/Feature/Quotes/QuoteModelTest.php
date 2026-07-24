<?php

use App\Domain\Organizations\Models\Organization;
use App\Domain\Organizations\Models\OrganizationMember;
use App\Domain\People\Models\Person;
use App\Domain\Quotes\Enums\QuoteStatus;
use App\Domain\Quotes\Enums\QuoteVersionRevisionType;
use App\Domain\Quotes\Enums\QuoteVersionStatus;
use App\Domain\Quotes\Models\Quote;
use App\Domain\Quotes\Models\QuoteItem;
use App\Domain\Quotes\Models\QuoteVersion;
use App\Domain\Quotes\Models\QuoteVersionRevision;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

function quoteAggregate(
    ?Organization $organization = null,
): array {
    $organization ??= Organization::factory()->create();
    $person = Person::factory()->for($organization)->create();
    $member = OrganizationMember::factory()->for($organization)->create();
    $quote = Quote::factory()->forOrganization($organization, $person)->create();
    $version = QuoteVersion::factory()->create([
        'organization_id' => $organization->id,
        'quote_id' => $quote->id,
        'created_by_organization_member_id' => $member->id,
    ]);
    $quote->update(['current_version_id' => $version->id]);

    return [$organization, $person, $member, $quote, $version];
}

it('persists the complete quotation aggregate and its relationships', function () {
    [$organization, $person, $member, $quote, $version] = quoteAggregate();
    $item = QuoteItem::factory()->create([
        'organization_id' => $organization->id,
        'quote_version_id' => $version->id,
    ]);
    $revision = QuoteVersionRevision::query()->create([
        'organization_id' => $organization->id,
        'quote_version_id' => $version->id,
        'changed_by_organization_member_id' => $member->id,
        'type' => QuoteVersionRevisionType::AdministrativeCorrection,
        'before_values' => ['contact_name' => 'Anterior'],
        'after_values' => ['contact_name' => 'Actual'],
    ]);

    expect($quote->status)->toBe(QuoteStatus::Draft)
        ->and($version->status)->toBe(QuoteVersionStatus::Draft)
        ->and($quote->person->is($person))->toBeTrue()
        ->and($quote->currentVersion->is($version))->toBeTrue()
        ->and($version->items()->sole()->is($item))->toBeTrue()
        ->and($version->revisions()->sole()->is($revision))->toBeTrue()
        ->and($revision->type)->toBe(QuoteVersionRevisionType::AdministrativeCorrection)
        ->and($organization->quotes()->sole()->is($quote))->toBeTrue();
});

it('keeps quotation numbers unique only within an organization', function () {
    $firstOrganization = Organization::factory()->create();
    $secondOrganization = Organization::factory()->create();

    Quote::factory()->forOrganization($firstOrganization)->create(['number' => 'COT-000001']);
    Quote::factory()->forOrganization($secondOrganization)->create(['number' => 'COT-000001']);

    expect(fn () => Quote::factory()
        ->forOrganization($firstOrganization)
        ->create(['number' => 'COT-000001']))
        ->toThrow(QueryException::class);
});

it('rejects cross-organization people at the database boundary', function () {
    $organization = Organization::factory()->create();
    $otherOrganization = Organization::factory()->create();
    $externalPerson = Person::factory()->for($otherOrganization)->create();

    expect(fn () => Quote::factory()->create([
        'organization_id' => $organization->id,
        'person_id' => $externalPerson->id,
    ]))->toThrow(QueryException::class);
});

it('rejects cross-organization versions, items and revisions at the database boundary', function () {
    [$organization, , $member, $quote, $version] = quoteAggregate();
    $otherOrganization = Organization::factory()->create();
    $otherMember = OrganizationMember::factory()->for($otherOrganization)->create();

    expect(fn () => QuoteVersion::factory()->create([
        'organization_id' => $otherOrganization->id,
        'quote_id' => $quote->id,
        'created_by_organization_member_id' => $otherMember->id,
    ]))->toThrow(QueryException::class);

    expect(fn () => QuoteItem::factory()->create([
        'organization_id' => $otherOrganization->id,
        'quote_version_id' => $version->id,
    ]))->toThrow(QueryException::class);

    expect(fn () => QuoteVersionRevision::query()->create([
        'organization_id' => $organization->id,
        'quote_version_id' => $version->id,
        'changed_by_organization_member_id' => $otherMember->id,
        'type' => QuoteVersionRevisionType::AdministrativeCorrection,
        'before_values' => [],
        'after_values' => [],
    ]))->toThrow(QueryException::class);

    expect($version->revisions()->count())->toBe(0)
        ->and($member->organization_id)->toBe($organization->id);
});

it('rejects assigning a version that belongs to another quotation', function () {
    [, , , $quote] = quoteAggregate();
    [, , , , $otherVersion] = quoteAggregate($quote->organization);

    expect(fn () => $quote->update([
        'current_version_id' => $otherVersion->id,
    ]))->toThrow(QueryException::class);
});

it('enforces quotation item bounds in the database', function () {
    [$organization, , , , $version] = quoteAggregate();

    expect(fn () => DB::table('quote_items')->insert([
        'organization_id' => $organization->id,
        'quote_version_id' => $version->id,
        'position' => 1,
        'name' => 'Inválido',
        'quantity' => 1,
        'unit' => 'servicio',
        'unit_price' => 100,
        'discount_amount' => 101,
        'tax_rate' => 16,
        'subtotal' => 0,
        'tax_amount' => 0,
        'total' => 0,
    ]))->toThrow(QueryException::class);
});

it('deletes the aggregate when its organization is deleted', function () {
    [$organization, , $member, $quote, $version] = quoteAggregate();
    $quote->update([
        'approved_version_id' => $version->id,
        'approved_by_organization_member_id' => $member->id,
    ]);
    QuoteItem::factory()->create([
        'organization_id' => $organization->id,
        'quote_version_id' => $version->id,
    ]);
    QuoteVersionRevision::query()->create([
        'organization_id' => $organization->id,
        'quote_version_id' => $version->id,
        'changed_by_organization_member_id' => $member->id,
        'type' => QuoteVersionRevisionType::AdministrativeCorrection,
        'before_values' => [],
        'after_values' => [],
    ]);

    $organization->delete();

    expect(Quote::query()->find($quote->id))->toBeNull()
        ->and(QuoteVersion::query()->find($version->id))->toBeNull()
        ->and(QuoteItem::query()->count())->toBe(0)
        ->and(QuoteVersionRevision::query()->count())->toBe(0);
});

it('preserves quotation history if an internal member is force deleted', function () {
    [$organization, , $member, $quote, $version] = quoteAggregate();
    $quote->update([
        'approved_version_id' => $version->id,
        'approved_by_organization_member_id' => $member->id,
    ]);
    $revision = QuoteVersionRevision::query()->create([
        'organization_id' => $organization->id,
        'quote_version_id' => $version->id,
        'changed_by_organization_member_id' => $member->id,
        'type' => QuoteVersionRevisionType::AdministrativeCorrection,
        'before_values' => [],
        'after_values' => [],
    ]);

    $member->forceDelete();

    expect($quote->refresh()->approved_by_organization_member_id)->toBeNull()
        ->and($version->refresh()->created_by_organization_member_id)->toBeNull()
        ->and($revision->refresh()->changed_by_organization_member_id)->toBeNull();
});

it('replaces the obsolete cancellation permission with archive', function () {
    $this->seed(AccessControlSeeder::class);

    $this->assertDatabaseHas('permissions', ['code' => 'quotes.archive']);
    $this->assertDatabaseMissing('permissions', ['code' => 'quotes.cancel']);
});
