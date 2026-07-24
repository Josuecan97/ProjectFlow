<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $quote->number }} - {{ $version->title }}</title>
    <style>
        @page { margin: 34px 42px 48px; }
        * { box-sizing: border-box; }
        body { color: #27272a; font-family: "DejaVu Sans", sans-serif; font-size: 10px; line-height: 1.45; }
        h1, h2, p { margin: 0; }
        .header { border-bottom: 2px solid #4f46e5; padding-bottom: 16px; }
        .brand { color: #18181b; font-size: 20px; font-weight: bold; }
        .folio { color: #4f46e5; font-size: 18px; font-weight: bold; text-align: right; }
        .muted { color: #71717a; }
        .grid { display: table; table-layout: fixed; width: 100%; }
        .cell { display: table-cell; vertical-align: top; }
        .right { text-align: right; }
        .section { margin-top: 20px; }
        .section-title { color: #3f3f46; font-size: 11px; font-weight: bold; letter-spacing: .4px; margin-bottom: 8px; text-transform: uppercase; }
        .card { background: #f4f4f5; border-radius: 5px; padding: 12px; }
        table { border-collapse: collapse; margin-top: 8px; width: 100%; }
        th { background: #27272a; color: white; font-size: 8px; padding: 8px 6px; text-align: left; text-transform: uppercase; }
        td { border-bottom: 1px solid #e4e4e7; padding: 8px 6px; vertical-align: top; }
        th.number, td.number { text-align: right; white-space: nowrap; }
        .totals { margin-left: 55%; margin-top: 12px; width: 45%; }
        .totals td { border: 0; padding: 4px 6px; }
        .total { border-top: 1px solid #a1a1aa !important; font-size: 12px; font-weight: bold; padding-top: 7px !important; }
        .content { white-space: pre-line; }
        .footer { bottom: -30px; color: #71717a; font-size: 8px; left: 0; position: fixed; right: 0; text-align: center; }
        .avoid-break { page-break-inside: avoid; }
    </style>
</head>
<body>
    <footer class="footer">{{ $quote->number }} · {{ __('Versión') }} {{ $version->version_number }}</footer>

    <header class="header grid">
        <div class="cell">
            <div class="brand">{{ $organization->name }}</div>
            @if ($organization->legal_name)<div>{{ $organization->legal_name }}</div>@endif
            @if ($organization->tax_id)<div class="muted">{{ __('RFC') }}: {{ $organization->tax_id }}</div>@endif
            @if ($organization->email)<div class="muted">{{ $organization->email }}</div>@endif
            @if ($organization->phone)<div class="muted">{{ $organization->phone }}</div>@endif
        </div>
        <div class="cell right">
            <div class="folio">{{ $quote->number }}</div>
            <div>{{ __('Cotización') }} · {{ __('Versión') }} {{ $version->version_number }}</div>
            <div class="muted">{{ __('Estado') }}: {{ $version->status->label() }}</div>
        </div>
    </header>

    <section class="section grid">
        <div class="cell" style="width: 62%; padding-right: 14px;">
            <div class="section-title">{{ __('Cliente') }}</div>
            <div class="card">
                <strong>{{ $version->client_name }}</strong>
                @if ($version->contact_name)<div>{{ $version->contact_name }}</div>@endif
                @if ($version->contact_email)<div>{{ $version->contact_email }}</div>@endif
                @if ($version->contact_phone)<div>{{ $version->contact_phone }}</div>@endif
                @if ($version->client_address)
                    <div class="muted">{{ implode(', ', array_filter($version->client_address)) }}</div>
                @endif
            </div>
        </div>
        <div class="cell">
            <div class="section-title">{{ __('Vigencia') }}</div>
            <div class="card">
                <div>{{ __('Emisión') }}: <strong>{{ $version->issued_on->format('d/m/Y') }}</strong></div>
                <div>{{ __('Vencimiento') }}: <strong>{{ $version->expires_on->format('d/m/Y') }}</strong></div>
                <div>{{ __('Moneda') }}: <strong>{{ $version->currency }}</strong></div>
            </div>
        </div>
    </section>

    <section class="section">
        <h1 style="font-size: 17px;">{{ $version->title }}</h1>
        @if ($version->description)<p class="muted content" style="margin-top: 5px;">{{ $version->description }}</p>@endif
    </section>

    <section class="section">
        <div class="section-title">{{ __('Conceptos') }}</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 38%;">{{ __('Concepto') }}</th>
                    <th class="number">{{ __('Cantidad') }}</th>
                    <th class="number">{{ __('Precio') }}</th>
                    <th class="number">{{ __('Descuento') }}</th>
                    <th class="number">{{ __('Impuesto') }}</th>
                    <th class="number">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($version->items as $item)
                    <tr>
                        <td><strong>{{ $item->name }}</strong>@if ($item->description)<div class="muted">{{ $item->description }}</div>@endif</td>
                        <td class="number">{{ $item->quantity }} {{ $item->unit }}</td>
                        <td class="number">{{ $calculator->display($item->unit_price) }}</td>
                        <td class="number">{{ $calculator->display($item->discount_amount) }}</td>
                        <td class="number">{{ $calculator->display($item->tax_amount) }}</td>
                        <td class="number">{{ $calculator->display($item->total) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <table class="totals avoid-break">
            <tr><td>{{ __('Subtotal') }}</td><td class="right">{{ $calculator->display($version->subtotal) }}</td></tr>
            <tr><td>{{ __('Descuentos') }}</td><td class="right">{{ $calculator->display($version->discount_total) }}</td></tr>
            <tr><td>{{ __('Impuestos') }}</td><td class="right">{{ $calculator->display($version->tax_total) }}</td></tr>
            <tr><td class="total">{{ __('Total') }}</td><td class="total right">{{ $version->currency }} {{ $calculator->display($version->total) }}</td></tr>
        </table>
    </section>

    @if ($version->scope)
        <section class="section avoid-break">
            <div class="section-title">{{ __('Alcance') }}</div>
            <div class="card content">{{ $version->scope }}</div>
        </section>
    @endif

    @if ($version->terms)
        <section class="section avoid-break">
            <div class="section-title">{{ __('Condiciones comerciales') }}</div>
            <div class="card content">{{ $version->terms }}</div>
        </section>
    @endif

    @if ($version->notes)
        <section class="section avoid-break">
            <div class="section-title">{{ __('Observaciones') }}</div>
            <div class="content">{{ $version->notes }}</div>
        </section>
    @endif
</body>
</html>
