<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceGenerated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice '.$this->invoice->invoice_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice-generated',
            with: ['invoice' => $this->invoice],
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        if ($this->invoice->xlsx_path) {
            $attachments[] = Attachment::fromStorageDisk('local', $this->invoice->xlsx_path)
                ->as($this->invoice->invoice_number.'.xlsx');
        }

        if ($this->invoice->pdf_path) {
            $attachments[] = Attachment::fromStorageDisk('local', $this->invoice->pdf_path)
                ->as($this->invoice->invoice_number.'.pdf');
        }

        $purchaseOrder = $this->invoice->purchaseOrder;

        if ($purchaseOrder?->file_path) {
            $attachments[] = Attachment::fromStorageDisk('local', $purchaseOrder->file_path)
                ->as($purchaseOrder->file_name);
        }

        return $attachments;
    }
}
