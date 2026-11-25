<?php

namespace App\Services;

use App\Models\Invoice;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class InvoiceEmailTemplateService
{
    private HtmlSanitizer $sanitizer;

    public function __construct(protected SettingService $settings)
    {
        $config = (new HtmlSanitizerConfig())
            ->allowSafeElements()
            ->allowRelativeLinks()
            ->allowRelativeMedias()
            ->allowElement('p')
            ->allowElement('br')
            ->allowElement('strong')
            ->allowElement('em')
            ->allowElement('ul')
            ->allowElement('ol')
            ->allowElement('li')
            ->allowElement('a', ['href', 'target', 'rel'])
            ->allowElement('h2')
            ->allowElement('h3')
            ->allowElement('blockquote')
            ->forceHttpsUrls();

        $this->sanitizer = new HtmlSanitizer($config);
    }

    public function render(Invoice $invoice): string
    {
        $ownerId = $invoice->created_by ? (string) $invoice->created_by : null;
        $template = (string) $this->settings->get(
            'invoice.email_template',
            $this->defaultTemplate(),
            $ownerId
        );

        $sanitized = $this->sanitizer->sanitize($template);

        $replacements = [
            '{{customer_name}}' => e($invoice->customer?->name ?? '-'),
            '{{invoice_number}}' => e($invoice->invoice_number),
            '{{invoice_date}}' => e($invoice->invoice_date?->locale('id')->translatedFormat('j F Y') ?? '-'),
            '{{period}}' => e($invoice->period?->locale('id')->translatedFormat('F Y') ?? '-'),
            '{{total}}' => e(number_format((float) $invoice->total, 2, ',', '.')),
            '{{company_name}}' => e((string) $this->settings->get('invoice.company_name', '', $ownerId)),
            '{{po_code}}' => e($invoice->po_code ?? $invoice->purchaseOrder?->invoice_po_code ?? '-'),
        ];

        return strtr($sanitized, $replacements);
    }

    public function defaultTemplate(): string
    {
        return '<p>Yth. {{customer_name}},</p><p>Invoice {{invoice_number}} untuk periode {{period}} telah dibuat.</p><p>Tanggal: {{invoice_date}}<br>Total: {{total}}</p><p>Terima kasih,<br>{{company_name}}</p>';
    }
}
