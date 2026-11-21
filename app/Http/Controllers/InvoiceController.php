<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Services\InvoiceGenerator;
use App\Services\InvoiceMailer;
use App\Services\SettingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceGenerator $generator,
        protected InvoiceMailer $mailer,
        protected SettingService $settings,
    ) {
        $this->middleware('permission:view-invoice')->only(['index', 'show', 'download']);
        $this->middleware('permission:create-invoice')->only(['generate']);
        $this->middleware('permission:send-invoice')->only(['send']);
        $this->middleware('permission:delete-invoice')->only(['destroy']);
    }

    public function index(): Response
    {
        $query = Invoice::with(['customer', 'purchaseOrder'])
            ->where('created_by', auth()->id());

        if (request('search')) {
            $search = request('search');
            $query->where(function ($invoiceQuery) use ($search) {
                $invoiceQuery->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        $purchaseOrders = PurchaseOrder::where('created_by', auth()->id())
            ->with('customer')
            ->latest('po_date')
            ->get()
            ->map(fn (PurchaseOrder $purchaseOrder) => [
                'id' => $purchaseOrder->id,
                'label' => $purchaseOrder->invoice_po_code.' - '.$purchaseOrder->customer?->name.' - '.$purchaseOrder->po_date->toDateString(),
            ]);

        return Inertia::render('Invoices/Index', [
            'invoices' => $query->latest('invoice_date')->paginate(request('per_page', 10))->withQueryString(),
            'purchaseOrders' => $purchaseOrders,
            'timezone' => (string) $this->settings->get('invoice.timezone', 'Asia/Makassar'),
            'filters' => [
                'search' => request('search', ''),
                'status' => request('status', ''),
                'per_page' => request('per_page', 10),
            ],
        ]);
    }

    public function show(Invoice $invoice): Response
    {
        $this->ensureOwned($invoice);
        $invoice->load(['customer', 'purchaseOrder', 'items']);

        return Inertia::render('Invoices/Show', [
            'invoice' => $invoice,
        ]);
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'date' => 'nullable|date',
            'purchase_order_id' => 'nullable|uuid|exists:purchase_orders,id',
            'include_installation' => 'boolean',
        ]);

        $timezone = (string) $this->settings->get('invoice.timezone', 'Asia/Makassar');
        $date = $data['date']
            ? Carbon::parse($data['date'], $timezone)->startOfDay()
            : Carbon::today($timezone);

        $includeInstallation = $request->boolean('include_installation');

        if ($data['purchase_order_id']) {
            $purchaseOrder = PurchaseOrder::where('created_by', auth()->id())
                ->findOrFail($data['purchase_order_id']);
            $this->generator->generateForPurchaseOrder($purchaseOrder, $date, $includeInstallation);
            $count = 1;
        } else {
            $count = $this->generator->generateForDate($date, true, $includeInstallation, (string) auth()->id())->count();
        }

        return redirect()->route('invoices.index')
            ->with('success', "Invoice berhasil dibuat: {$count}.");
    }

    public function send(Invoice $invoice)
    {
        $this->ensureOwned($invoice);
        $this->mailer->send($invoice);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice berhasil dikirim.');
    }

    public function download(Invoice $invoice, string $type)
    {
        $this->ensureOwned($invoice);
        abort_unless(in_array($type, ['xlsx', 'pdf'], true), 404);

        $path = $invoice->{$type.'_path'};

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path, $invoice->invoice_number.'.'.$type);
    }

    public function destroy(Invoice $invoice)
    {
        $this->ensureOwned($invoice);

        foreach (['xlsx_path', 'pdf_path'] as $field) {
            if ($invoice->{$field}) {
                Storage::disk('local')->delete($invoice->{$field});
            }
        }

        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice berhasil dihapus.');
    }

    protected function ensureOwned(Invoice $invoice): void
    {
        abort_unless((string) $invoice->created_by === (string) auth()->id(), 404);
    }
}
