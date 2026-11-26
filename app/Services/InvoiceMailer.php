<?php

namespace App\Services;

use App\Mail\InvoiceGenerated;
use App\Models\Invoice;
use Illuminate\Support\Facades\Mail;

class InvoiceMailer
{
    public function send(Invoice $invoice): void
    {
        $invoice->loadMissing(['customer', 'purchaseOrder']);

        $to = $invoice->customer?->email;
        $cc = collect(preg_split('/[;,]+/', (string) ($invoice->customer?->email_cc ?? '')) ?: [])
            ->map(fn ($email) => trim((string) $email))
            ->filter()
            ->reject(fn ($email) => strcasecmp($email, (string) $to) === 0)
            ->unique(fn ($email) => strtolower($email))
            ->values()
            ->all();

        $mail = Mail::to($to);

        if ($cc !== []) {
            $mail->cc($cc);
        }

        $mail->send(new InvoiceGenerated($invoice));

        $invoice->update([
            'sent_at' => now(),
            'status' => 'sent',
        ]);
    }
}
