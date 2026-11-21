<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InvoiceOwnershipTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminSeeder::class);
    }

    public function test_index_only_returns_invoices_owned_by_current_user(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('admin-invoice');

        $other = User::factory()->create();
        $other->assignRole('admin-invoice');

        $ownedInvoice = $this->createInvoice($owner, 'INVA0001');
        $otherInvoice = $this->createInvoice($other, 'INVB0001');

        $this->actingAs($owner)->get(route('invoices.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('invoices.total', 1)
                ->where('invoices.data.0.id', $ownedInvoice->id));

        $this->get(route('invoices.show', $otherInvoice))->assertNotFound();
    }

    private function createInvoice(User $owner, string $number): Invoice
    {
        $customer = Customer::create([
            'code' => $number,
            'name' => 'Customer '.$number,
            'email' => strtolower($number).'@example.com',
        ]);

        $purchaseOrder = PurchaseOrder::create([
            'customer_id' => $customer->id,
            'invoice_po_code' => '003',
            'po_date' => '2025-11-01',
            'file_path' => 'po/test.pdf',
            'file_name' => 'test.pdf',
        ]);

        return Invoice::create([
            'customer_id' => $customer->id,
            'purchase_order_id' => $purchaseOrder->id,
            'invoice_number' => $number,
            'period' => '2025-11-01',
            'invoice_date' => '2025-11-01',
            'sequence' => 1,
            'po_code' => '003',
            'subtotal' => 0,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'total' => 0,
            'status' => 'generated',
            'created_by' => $owner->id,
        ]);
    }
}
