<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Services;

use App\Domain\Quotes\Models\QuoteVersion;
use Dompdf\Dompdf;
use Dompdf\Options;

final class QuotePdfRenderer
{
    public function __construct(private readonly QuoteCalculator $calculator) {}

    public function render(QuoteVersion $version): string
    {
        $options = new Options;
        $options->set('isRemoteEnabled', false);
        $options->set('isPhpEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml(view('pdf.quote', [
            'version' => $version,
            'quote' => $version->quote,
            'organization' => $version->organization,
            'calculator' => $this->calculator,
        ])->render(), 'UTF-8');
        $dompdf->render();

        return $dompdf->output();
    }
}
