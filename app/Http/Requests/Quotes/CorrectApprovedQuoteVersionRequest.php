<?php

declare(strict_types=1);

namespace App\Http\Requests\Quotes;

use App\Domain\Quotes\Models\QuoteVersion;
use App\Domain\Quotes\Support\AdministrativeCorrectionRules;
use Illuminate\Foundation\Http\FormRequest;

final class CorrectApprovedQuoteVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $version = $this->route('quoteVersion');

        return $version instanceof QuoteVersion
            && ($this->user()?->can('update', $version->quote) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return AdministrativeCorrectionRules::rules();
    }
}
