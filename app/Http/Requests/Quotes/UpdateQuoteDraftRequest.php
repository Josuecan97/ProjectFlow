<?php

declare(strict_types=1);

namespace App\Http\Requests\Quotes;

use App\Domain\Quotes\Models\Quote;
use App\Domain\Quotes\Support\QuoteDraftRules;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateQuoteDraftRequest extends FormRequest
{
    public function authorize(): bool
    {
        $quote = $this->route('quote');

        return $quote instanceof Quote
            && ($this->user()?->can('update', $quote) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Quote $quote */
        $quote = $this->route('quote');

        return QuoteDraftRules::forOrganization($quote->organization_id);
    }
}
