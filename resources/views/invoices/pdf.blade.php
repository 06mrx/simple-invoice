<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #111; padding-bottom: 10px; }
        .company { font-size: 16px; font-weight: bold; }
        .invoice-title { text-align: right; }
        .meta { margin-top: 16px; display: flex; justify-content: space-between; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th, td { border: 1px solid #999; padding: 6px; vertical-align: top; }
        th { background: #eee; text-align: left; }
        .num { text-align: right; }
        .totals { margin-top: 12px; margin-left: auto; width: 260px; }
        .totals td { border: none; padding: 4px 6px; }
        .footer { margin-top: 28px; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="company">{{ $settings['invoice.company_name'] ?? '' }}</div>
            <div>NPWP : {{ $settings['invoice.npwp'] ?? '' }}</div>
            <div>{{ $settings['invoice.address'] ?? '' }}</div>
            <div>{{ $settings['invoice.city'] ?? '' }}</div>
            <div>{{ $settings['invoice.phone'] ?? '' }} | {{ $settings['invoice.email'] ?? '' }}</div>
        </div>
        <div class="invoice-title">
            <h2>INVOICE</h2>
            <div>{{ $invoice->invoice_number }}</div>
            <div>{{ $invoice->invoice_date->format('d/m/Y') }}</div>
        </div>
    </div>

    <div class="meta">
        <div>
            <strong>BILL TO</strong>
            <div>{{ $invoice->customer->name }}</div>
            <div>{{ $invoice->customer->address }}</div>
            <div>{{ $invoice->customer->phone }}</div>
            <div>E : {{ $invoice->customer->email }}</div>
        </div>
        <div>
            <strong>CUSTOMER ID</strong>
            <div>{{ $invoice->customer->code }}</div>
            <br>
            <strong>TYPE</strong>
            <div>Transfer {{ $settings['invoice.bank_name'] ?? '' }} ke</div>
            <div>{{ $settings['invoice.bank_account'] ?? '' }}</div>
            <div>a.n {{ $settings['invoice.bank_account_name'] ?? '' }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>DESCRIPTION</th>
                <th class="num">QTY</th>
                <th class="num">UNIT PRICE</th>
                <th class="num">AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="num">{{ (float) $item->qty }}</td>
                    <td class="num">{{ number_format((float) $item->unit_price, 2) }}</td>
                    <td class="num">{{ number_format((float) $item->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>SUBTOTAL</td>
            <td class="num">{{ number_format((float) $invoice->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>TAX RATE</td>
            <td class="num">{{ (float) $invoice->tax_rate }}</td>
        </tr>
        <tr>
            <td>TAX</td>
            <td class="num">{{ number_format((float) $invoice->tax_amount, 2) }}</td>
        </tr>
        <tr>
            <td><strong>TOTAL</strong></td>
            <td class="num"><strong>{{ number_format((float) $invoice->total, 2) }}</strong></td>
        </tr>
    </table>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>
            If you have any questions about this invoice, please contact
            {{ $settings['invoice.contact_name'] ?? '' }},
            {{ $settings['invoice.phone'] ?? '' }},
            {{ $settings['invoice.email'] ?? '' }}
        </p>
    </div>
</body>
</html>
