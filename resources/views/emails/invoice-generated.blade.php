<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
</head>
<body>
    <p>Yth. {{ $invoice->customer->name }},</p>

    <p>Invoice {{ $invoice->invoice_number }} untuk periode {{ $invoice->period->format('F Y') }} telah dibuat.</p>

    <ul>
        <li>Nomor Invoice: {{ $invoice->invoice_number }}</li>
        <li>Tanggal: {{ $invoice->invoice_date->format('d/m/Y') }}</li>
        <li>Total: {{ number_format((float) $invoice->total, 2) }}</li>
    </ul>

    <p>Terlampir file invoice (XLSX dan PDF) serta dokumen PO pada lampiran email ini.</p>

    <p>Terima kasih atas kerja samanya.</p>
</body>
</html>
