<?php

declare(strict_types=1);

namespace App\Http\Controllers\Quotes;

use App\Domain\Organizations\Support\CurrentOrganization;
use App\Domain\Quotes\Actions\GenerateQuotePdf;
use App\Domain\Quotes\Models\Quote;
use App\Domain\Quotes\Models\QuoteVersion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

final class QuotePdfController extends Controller
{
    public function __invoke(
        Quote $quote,
        QuoteVersion $quoteVersion,
        CurrentOrganization $currentOrganization,
        GenerateQuotePdf $generatePdf,
    ): Response {
        abort_unless(
            $quote->organization_id === $currentOrganization->id()
            && $quoteVersion->organization_id === $currentOrganization->id()
            && $quoteVersion->quote_id === $quote->id,
            404,
        );

        $pdf = $generatePdf->handle(request()->user(), $quoteVersion);

        return response($pdf->content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf('inline; filename="%s"', $pdf->filename),
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
