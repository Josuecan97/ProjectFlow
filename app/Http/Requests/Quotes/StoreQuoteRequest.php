<?php

declare(strict_types=1);

namespace App\Http\Requests\Quotes;

use App\Domain\Organizations\Support\CurrentOrganization;
use App\Domain\Quotes\Models\Quote;
use App\Domain\Quotes\Support\QuoteDraftRules;
use Illuminate\Foundation\Http\FormRequest;

final class StoreQuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $organization = app(CurrentOrganization::class)->get();

        return $user?->can('create', [Quote::class, $organization]) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return QuoteDraftRules::forOrganization(app(CurrentOrganization::class)->id());
    }
}
