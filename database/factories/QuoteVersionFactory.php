<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Organizations\Models\OrganizationMember;
use App\Domain\Quotes\Enums\QuoteVersionStatus;
use App\Domain\Quotes\Models\Quote;
use App\Domain\Quotes\Models\QuoteVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuoteVersion>
 */
final class QuoteVersionFactory extends Factory
{
    protected $model = QuoteVersion::class;

    public function definition(): array
    {
        return [
            'quote_id' => Quote::factory(),
            'organization_id' => fn (array $attributes): int => Quote::query()
                ->findOrFail($attributes['quote_id'])
                ->organization_id,
            'version_number' => 1,
            'status' => QuoteVersionStatus::Draft,
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->sentence(),
            'issued_on' => now()->toDateString(),
            'expires_on' => now()->addDays(15)->toDateString(),
            'currency' => 'MXN',
            'client_name' => fake()->company(),
            'subtotal' => '0.000000',
            'discount_total' => '0.000000',
            'tax_total' => '0.000000',
            'total' => '0.000000',
            'created_by_organization_member_id' => function (array $attributes): int {
                return OrganizationMember::factory()->create([
                    'organization_id' => $attributes['organization_id'],
                ])->id;
            },
        ];
    }
}
