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

        Mail::to($invoice->customer->email)->send(new InvoiceGenerated($invoice));

        $invoice->update([
            'sent_at' => now(),
            'status' => 'sent',
        ]);
    }
}
