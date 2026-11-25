<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class InvoiceGenerator
{
    public function __construct(
        protected SettingService $settings,
        protected NumberSequenceService $sequences,
        protected InvoiceNumberService $numbers,
        protected InvoiceXlsxWriter $xlsxWriter,
        protected InvoicePdfWriter $pdfWriter,
        protected InvoiceMailer $mailer,
    ) {
    }

    public function generateForDate(CarbonInterface $date, bool $force = false, bool $includeInstallation = false, ?string $ownerId = null): Collection
    {
        $query = PurchaseOrder::with(['customer', 'items', 'installationItems']);

        if ($ownerId !== null) {
            $query->where('created_by', $ownerId);
        }

        $purchaseOrders = $query
            ->get()
            ->groupBy('customer_id')
            ->map(fn (Collection $group) => $group->sortByDesc('po_date')->first())
            ->filter();

        if (! $force) {
            $purchaseOrders = $purchaseOrders->filter(function (PurchaseOrder $purchaseOrder) use ($date) {
                $ownerId = $purchaseOrder->created_by ? (string) $purchaseOrder->created_by : null;
                $mode = (string) $this->settings->get('invoice.schedule_mode', 'generate_day', $ownerId);

                if ($mode === 'po_day') {
                    return $purchaseOrder->po_date
                        ? $date->day === (int) $purchaseOrder->po_date->day
                        : false;
                }

                return $date->day === (int) $this->settings->get('invoice.generate_day', 1, $ownerId);
            });
        }

        return $purchaseOrders
            ->map(fn (PurchaseOrder $purchaseOrder) => $this->generateForPurchaseOrder($purchaseOrder, $date, $includeInstallation, $force))
            ->values();
    }

    public function generateForPurchaseOrder(PurchaseOrder $purchaseOrder, ?CarbonInterface $date = null, bool $includeInstallation = false, bool $force = false): Invoice
    {
        $ownerId = $purchaseOrder->created_by ? (string) $purchaseOrder->created_by : null;
        $date ??= Carbon::now((string) $this->settings->get('invoice.timezone', 'Asia/Makassar', $ownerId));
        $period = $date->copy()->startOfMonth()->toDateString();
        $taxRate = (float) $this->settings->get('invoice.tax_rate', 0, $ownerId);

        $invoice = DB::transaction(function () use ($purchaseOrder, $date, $period, $taxRate, $includeInstallation, $ownerId, $force) {
            $customer = Customer::where('id', $purchaseOrder->customer_id)->lockForUpdate()->firstOrFail();

            if (! $force) {
                $existing = Invoice::where('customer_id', $customer->id)
                    ->where('period', $period)
                    ->where('created_by', $ownerId)
                    ->first();

                if ($existing) {
                    return $existing;
                }
            }

            $lastSequence = (int) $this->settings->get('invoice.last_invoice_sequence', 1669, $ownerId);
            $sequence = $this->sequences->next('invoice', $lastSequence + 1);
            $invoiceNumber = $this->numbers->format($date, $purchaseOrder->invoice_po_code, $sequence);

            $invoice = Invoice::create([
                'customer_id' => $customer->id,
                'purchase_order_id' => $purchaseOrder->id,
                'invoice_number' => $invoiceNumber,
                'period' => $period,
                'invoice_date' => $date->toDateString(),
                'sequence' => $sequence,
                'po_code' => $purchaseOrder->invoice_po_code,
                'subtotal' => 0,
                'tax_rate' => $taxRate,
                'tax_amount' => 0,
                'total' => 0,
                'status' => 'pending',
                'created_by' => $purchaseOrder->created_by,
            ]);

            $sortOrder = 0;

            foreach ($purchaseOrder->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'kind' => 'service',
                    'description' => $item->description,
                    'qty' => $item->qty,
                    'unit_price' => $item->unit_price,
                    'amount' => $item->amount,
                    'sort_order' => $sortOrder++,
                ]);
            }

            if ($includeInstallation) {
                foreach ($purchaseOrder->installationItems as $item) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'kind' => 'installation',
                        'description' => $item->name,
                        'qty' => $item->qty,
                        'unit_price' => $item->price,
                        'amount' => round((float) $item->qty * (float) $item->price, 2),
                        'sort_order' => $sortOrder++,
                    ]);
                }
            }

            $subtotal = (float) $invoice->items()->sum('amount');
            $taxAmount = round($subtotal * $taxRate, 2);

            $invoice->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total' => $subtotal + $taxAmount,
            ]);

            return $invoice;
        });

        if (! $invoice->xlsx_path || ! $invoice->pdf_path) {
            try {
                $xlsxPath = $this->xlsxWriter->write($invoice);
                $pdfPath = $this->pdfWriter->write($invoice, $xlsxPath);

                $invoice->update([
                    'xlsx_path' => $xlsxPath,
                    'pdf_path' => $pdfPath,
                    'status' => 'generated',
                ]);
            } catch (Throwable $exception) {
                $invoice->update(['status' => 'failed']);

                throw $exception;
            }
        }

        if (! $invoice->sent_at) {
            $this->mailer->send($invoice);
        }

        return $invoice->refresh();
    }
}
