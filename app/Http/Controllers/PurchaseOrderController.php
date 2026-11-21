<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PurchaseOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-purchaseorder')->only(['index', 'show', 'download']);
        $this->middleware('permission:create-purchaseorder')->only(['create', 'store']);
        $this->middleware('permission:edit-purchaseorder')->only(['edit', 'update']);
        $this->middleware('permission:delete-purchaseorder')->only(['destroy']);
    }

    public function index(): Response
    {
        $query = PurchaseOrder::with('customer')->where('created_by', auth()->id());

        if (request('search')) {
            $search = request('search');
            $query->where(function ($purchaseOrderQuery) use ($search) {
                $purchaseOrderQuery->where('invoice_po_code', 'like', "%{$search}%")
                    ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        if (request('customer_id')) {
            $query->where('customer_id', request('customer_id'));
        }

        return Inertia::render('PurchaseOrders/Index', [
            'purchaseOrders' => $query->latest('po_date')->paginate(request('per_page', 10))->withQueryString(),
            'customers' => $this->customers(),
            'filters' => [
                'search' => request('search', ''),
                'customer_id' => request('customer_id', ''),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('PurchaseOrders/Create', [
            'customers' => $this->customers(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request, true);

        $purchaseOrder = DB::transaction(function () use ($data, $request) {
            $path = $this->storeFile($request->file('file'), $data['customer_id']);

            $purchaseOrder = PurchaseOrder::create([
                'customer_id' => $data['customer_id'],
                'invoice_po_code' => $data['invoice_po_code'],
                'po_date' => $data['po_date'],
                'file_path' => $path,
                'file_name' => $request->file('file')->getClientOriginalName(),
                'notes' => $data['notes'] ?? null,
            ]);

            $this->syncItems($purchaseOrder, $data['items']);
            $this->syncInstallationItems($purchaseOrder, $data['installation_items'] ?? []);

            return $purchaseOrder;
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'PO berhasil ditambahkan.');
    }

    public function show(PurchaseOrder $purchaseOrder): Response
    {
        $this->ensureOwned($purchaseOrder);
        $purchaseOrder->load(['customer', 'items', 'installationItems']);

        return Inertia::render('PurchaseOrders/Show', [
            'purchaseOrder' => $purchaseOrder,
        ]);
    }

    public function edit(PurchaseOrder $purchaseOrder): Response
    {
        $this->ensureOwned($purchaseOrder);
        $purchaseOrder->load(['items', 'installationItems']);

        return Inertia::render('PurchaseOrders/Edit', [
            'purchaseOrder' => $purchaseOrder,
            'customers' => $this->customers(),
        ]);
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->ensureOwned($purchaseOrder);
        $data = $this->validateRequest($request, false);

        DB::transaction(function () use ($data, $request, $purchaseOrder) {
            $fileData = [];

            if ($request->hasFile('file')) {
                if ($purchaseOrder->file_path) {
                    Storage::disk('local')->delete($purchaseOrder->file_path);
                }

                $fileData = [
                    'file_path' => $this->storeFile($request->file('file'), $data['customer_id']),
                    'file_name' => $request->file('file')->getClientOriginalName(),
                ];
            }

            $purchaseOrder->update(array_merge([
                'customer_id' => $data['customer_id'],
                'invoice_po_code' => $data['invoice_po_code'],
                'po_date' => $data['po_date'],
                'notes' => $data['notes'] ?? null,
            ], $fileData));

            $purchaseOrder->items()->delete();
            $this->syncItems($purchaseOrder, $data['items']);

            if (array_key_exists('installation_items', $data)) {
                $purchaseOrder->installationItems()->delete();
                $this->syncInstallationItems($purchaseOrder, $data['installation_items']);
            }
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'PO berhasil diperbarui.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $this->ensureOwned($purchaseOrder);

        if ($purchaseOrder->file_path) {
            Storage::disk('local')->delete($purchaseOrder->file_path);
        }

        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')
            ->with('success', 'PO berhasil dihapus.');
    }

    public function download(PurchaseOrder $purchaseOrder)
    {
        $this->ensureOwned($purchaseOrder);
        abort_unless($purchaseOrder->file_path && Storage::disk('local')->exists($purchaseOrder->file_path), 404);

        return Storage::disk('local')->download($purchaseOrder->file_path, $purchaseOrder->file_name);
    }

    protected function customers()
    {
        return Customer::where('created_by', auth()->id())->orderBy('code')->get(['id', 'code', 'name']);
    }

    protected function validateRequest(Request $request, bool $requireFile): array
    {
        $data = $request->validate([
            'customer_id' => [
                'required',
                'uuid',
                Rule::exists('customers', 'id')->where(fn ($query) => $query->where('created_by', auth()->id())),
            ],
            'invoice_po_code' => 'required|string|regex:/^\d{3}$/',
            'po_date' => 'required|date',
            'file' => ($requireFile ? 'required' : 'nullable').'|file|mimes:pdf|max:10240',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.qty' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'installation_items' => 'nullable|array',
            'installation_items.*.name' => 'required|string|max:255',
            'installation_items.*.qty' => 'required|numeric|min:0',
            'installation_items.*.price' => 'required|numeric|min:0',
        ]);

        return $data;
    }

    protected function storeFile($file, string $customerId): string
    {
        $extension = $file->getClientOriginalExtension() ?: 'pdf';
        $fileName = Str::uuid().'.'.$extension;

        return $file->storeAs('po/'.$customerId.'/'.date('Y'), $fileName, 'local');
    }

    protected function syncItems(PurchaseOrder $purchaseOrder, array $items): void
    {
        foreach (array_values($items) as $index => $item) {
            PurchaseOrderItem::create([
                'po_id' => $purchaseOrder->id,
                'description' => $item['description'],
                'qty' => $item['qty'],
                'unit_price' => $item['unit_price'],
                'amount' => (float) $item['qty'] * (float) $item['unit_price'],
                'sort_order' => $index,
            ]);
        }
    }

    protected function syncInstallationItems(PurchaseOrder $purchaseOrder, array $items): void
    {
        foreach (array_values($items) as $index => $item) {
            $purchaseOrder->installationItems()->create([
                'name' => $item['name'],
                'qty' => $item['qty'],
                'price' => $item['price'],
                'sort_order' => $index,
            ]);
        }
    }

    private function ensureOwned(PurchaseOrder $purchaseOrder): void
    {
        abort_unless((string) $purchaseOrder->created_by === (string) auth()->id(), 404);
    }
}
