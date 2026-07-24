<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Actions;

use App\Domain\Quotes\Models\QuoteVersion;
use App\Domain\Quotes\Services\QuotePdfRenderer;
use App\Domain\Quotes\ValueObjects\GeneratedQuotePdf;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

final class GenerateQuotePdf
{
    public function __construct(private readonly QuotePdfRenderer $renderer) {}

    public function handle(User $actor, QuoteVersion $version): GeneratedQuotePdf
    {
        Gate::forUser($actor)->authorize('view', $version->quote);
        $version->loadMissing(['quote.person', 'organization', 'items']);

        return new GeneratedQuotePdf(
            sprintf('%s-v%d.pdf', $version->quote->number, $version->version_number),
            $this->renderer->render($version),
        );
    }
}
