<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\InstallationItem;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->seed(AdminSeeder::class);
    }

    public function test_admin_invoice_gets_invoice_dashboard_with_own_data(): void
    {
        $owner = $this->userWithRole('admin-invoice');
        $other = $this->userWithRole('admin-invoice');

        $this->actingAs($owner);
        $ownerCustomer = Customer::create([
            'code' => 'DASH-OWNER',
            'name' => 'Customer Owner',
            'email' => 'dash-owner@example.com',
        ]);

        $ownerPurchaseOrder = PurchaseOrder::create([
            'customer_id' => $ownerCustomer->id,
            'invoice_po_code' => '001',
            'po_date' => now()->toDateString(),
            'file_path' => 'po/owner.pdf',
            'file_name' => 'owner.pdf',
        ]);

        InstallationItem::create([
            'po_id' => $ownerPurchaseOrder->id,
            'name' => 'Instalasi',
            'qty' => 1,
            'price' => 100000,
            'sort_order' => 1,
        ]);

        $ownerInvoice = Invoice::create([
            'customer_id' => $ownerCustomer->id,
            'purchase_order_id' => $ownerPurchaseOrder->id,
            'invoice_number' => 'INV/001',
            'period' => now()->toDateString(),
            'invoice_date' => now()->toDateString(),
            'sequence' => 1,
            'po_code' => 'PO-001',
            'subtotal' => 1500000,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'total' => 1500000,
            'status' => 'sent',
        ]);

        $this->actingAs($other);
        $otherCustomer = Customer::create([
            'code' => 'DASH-OTHER',
            'name' => 'Customer Other',
            'email' => 'dash-other@example.com',
        ]);

        $otherPurchaseOrder = PurchaseOrder::create([
            'customer_id' => $otherCustomer->id,
            'invoice_po_code' => '002',
            'po_date' => now()->toDateString(),
            'file_path' => 'po/other.pdf',
            'file_name' => 'other.pdf',
        ]);

        Invoice::create([
            'customer_id' => $otherCustomer->id,
            'purchase_order_id' => $otherPurchaseOrder->id,
            'invoice_number' => 'INV/002',
            'period' => now()->toDateString(),
            'invoice_date' => now()->toDateString(),
            'sequence' => 2,
            'po_code' => 'PO-002',
            'subtotal' => 99000000,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'total' => 99000000,
            'status' => 'failed',
        ]);

        $this->actingAs($owner)->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard/Invoice')
                ->where('metrics.total_invoices', 1)
                ->where('metrics.total_revenue', fn (mixed $value) => (float) $value === 1500000.0)
                ->where('metrics.sent_count', 1)
                ->where('metrics.total_customers', 1)
                ->where('metrics.total_purchase_orders', 1)
                ->where('metrics.installation_purchase_orders', 1)
                ->where('status_counts.sent', 1)
                ->where('recent_invoices.0.id', $ownerInvoice->id)
                ->where('recent_customers.0.id', $ownerCustomer->id));
    }

    public function test_admin_gets_default_dashboard(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Dashboard'));
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
