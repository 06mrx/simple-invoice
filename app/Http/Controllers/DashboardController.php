<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Services\SettingService;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(protected SettingService $settings)
    {
    }

    public function __invoke(): Response
    {
        $user = auth()->user();

        if ($user instanceof User && $user->hasRole('admin-invoice')) {
            return $this->invoiceDashboard($user);
        }

        return Inertia::render('Dashboard');
    }

    private function invoiceDashboard(User $user): Response
    {
        $ownerId = (string) $user->id;
        $timezone = (string) $this->settings->get('invoice.timezone', 'Asia/Makassar', $ownerId);
        $now = Carbon::now($timezone);

        $invoices = Invoice::where('created_by', $ownerId);
        $customers = Customer::where('created_by', $ownerId);
        $purchaseOrders = PurchaseOrder::where('created_by', $ownerId);

        $totalInvoices = (clone $invoices)->count();
        $totalRevenue = (float) (clone $invoices)->sum('total');
        $sentCount = (clone $invoices)->where('status', 'sent')->count();
        $sentRevenue = (float) (clone $invoices)->where('status', 'sent')->sum('total');
        $outstanding = (float) (clone $invoices)->where('status', '!=', 'sent')->sum('total');

        $statusRows = (clone $invoices)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusCounts = [
            'generated' => (int) ($statusRows['generated'] ?? 0),
            'sent' => (int) ($statusRows['sent'] ?? 0),
            'failed' => (int) ($statusRows['failed'] ?? 0),
        ];

        $currentStart = $now->copy()->startOfMonth();
        $previousStart = $now->copy()->subMonth()->startOfMonth();
        $previousEnd = $now->copy()->subMonth()->endOfMonth();

        $currentCount = (clone $invoices)->whereBetween('invoice_date', [$currentStart, $now])->count();
        $currentRevenue = (float) (clone $invoices)->whereBetween('invoice_date', [$currentStart, $now])->sum('total');
        $currentSent = (clone $invoices)->whereBetween('invoice_date', [$currentStart, $now])->where('status', 'sent')->count();

        $previousCount = (clone $invoices)->whereBetween('invoice_date', [$previousStart, $previousEnd])->count();
        $previousRevenue = (float) (clone $invoices)->whereBetween('invoice_date', [$previousStart, $previousEnd])->sum('total');
        $previousSent = (clone $invoices)->whereBetween('invoice_date', [$previousStart, $previousEnd])->where('status', 'sent')->count();

        $monthlyStart = $now->copy()->startOfMonth()->subMonths(5);
        $monthlyRows = (clone $invoices)
            ->where('invoice_date', '>=', $monthlyStart)
            ->get(['invoice_date', 'total']);

        $monthly = collect(range(0, 5))->map(function (int $offset) use ($monthlyStart, $monthlyRows) {
            $month = $monthlyStart->copy()->addMonths($offset);
            $rows = $monthlyRows->filter(
                fn (Invoice $invoice) => $invoice->invoice_date->format('Y-m') === $month->format('Y-m')
            );

            return [
                'label' => $this->monthLabel((int) $month->format('n')),
                'total' => (float) $rows->sum('total'),
                'count' => $rows->count(),
            ];
        })->all();

        $recentInvoices = (clone $invoices)
            ->with(['customer:id,code,name', 'purchaseOrder:id,invoice_po_code'])
            ->latest('invoice_date')
            ->limit(8)
            ->get()
            ->map(fn (Invoice $invoice) => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'invoice_date' => $invoice->invoice_date->toDateString(),
                'total' => (float) $invoice->total,
                'status' => $invoice->status,
                'customer' => [
                    'code' => $invoice->customer?->code ?? '-',
                    'name' => $invoice->customer?->name ?? '-',
                ],
                'po_code' => $invoice->purchaseOrder?->invoice_po_code ?? $invoice->po_code,
            ])
            ->all();

        $recentCustomers = (clone $customers)
            ->latest()
            ->limit(5)
            ->get(['id', 'code', 'name', 'email', 'created_at'])
            ->map(fn (Customer $customer) => [
                'id' => $customer->id,
                'code' => $customer->code,
                'name' => $customer->name,
                'email' => $customer->email,
                'created_at' => $customer->created_at->toDateString(),
            ])
            ->all();

        $topCustomers = (clone $invoices)
            ->selectRaw('customer_id, count(*) as invoice_count, coalesce(sum(total), 0) as revenue')
            ->groupBy('customer_id')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get()
            ->map(function (Invoice $invoice) {
                return [
                    'id' => $invoice->customer_id,
                    'name' => $invoice->customer?->name ?? '-',
                    'code' => $invoice->customer?->code ?? '-',
                    'invoice_count' => (int) $invoice->invoice_count,
                    'revenue' => (float) $invoice->revenue,
                ];
            })
            ->all();

        return Inertia::render('Dashboard/Invoice', [
            'period' => $currentStart->format('d M Y').' - '.$now->format('d M Y'),
            'metrics' => [
                'total_invoices' => $totalInvoices,
                'total_revenue' => $totalRevenue,
                'sent_count' => $sentCount,
                'sent_revenue' => $sentRevenue,
                'outstanding' => $outstanding,
                'failed_count' => $statusCounts['failed'],
                'delivery_rate' => $totalInvoices > 0 ? round(($sentCount / $totalInvoices) * 100, 1) : 0,
                'total_customers' => (clone $customers)->count(),
                'total_purchase_orders' => (clone $purchaseOrders)->count(),
                'installation_purchase_orders' => (clone $purchaseOrders)->whereHas('installationItems')->count(),
            ],
            'trends' => [
                'invoices' => $this->trend($currentCount, $previousCount),
                'revenue' => $this->trend($currentRevenue, $previousRevenue),
                'sent' => $this->trend($currentSent, $previousSent),
            ],
            'status_counts' => $statusCounts,
            'monthly' => $monthly,
            'recent_invoices' => $recentInvoices,
            'recent_customers' => $recentCustomers,
            'top_customers' => $topCustomers,
            'purchase_orders' => [
                'total' => (clone $purchaseOrders)->count(),
                'this_month' => (clone $purchaseOrders)->whereBetween('po_date', [$currentStart, $now])->count(),
            ],
        ]);
    }

    private function trend(float $current, float $previous): array
    {
        return [
            'current' => $current,
            'previous' => $previous,
            'percent' => $this->percentChange($current, $previous),
        ];
    }

    private function percentChange(float $current, float $previous): float
    {
        if ($previous <= 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function monthLabel(int $month): string
    {
        return ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'][$month - 1];
    }
}
