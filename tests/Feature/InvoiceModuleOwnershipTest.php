<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Services\SettingService;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InvoiceModuleOwnershipTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->seed(AdminSeeder::class);
        Storage::fake('local');
    }

    public function test_customers_are_scoped_per_user_and_unique_per_user(): void
    {
        $owner = $this->invoiceUser();
        $other = $this->invoiceUser();

        $this->actingAs($owner)->post(route('customers.store'), [
            'code' => 'CUST001',
            'name' => 'Customer Owner',
            'email' => 'same@example.com',
        ])->assertRedirect(route('customers.index'))->assertSessionHasNoErrors();

        $this->actingAs($other)->post(route('customers.store'), [
            'code' => 'CUST001',
            'name' => 'Customer Other',
            'email' => 'same@example.com',
        ])->assertRedirect(route('customers.index'))->assertSessionHasNoErrors();

        $ownedCustomer = Customer::where('created_by', (string) $owner->id)->firstOrFail();
        $otherCustomer = Customer::where('created_by', (string) $other->id)->firstOrFail();

        $this->actingAs($owner)->get(route('customers.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('customers.total', 1)
                ->where('customers.data.0.id', $ownedCustomer->id));

        $this->get(route('customers.edit', $otherCustomer))->assertNotFound();
    }

    public function test_purchase_orders_are_scoped_per_user(): void
    {
        $owner = $this->invoiceUser();
        $other = $this->invoiceUser();

        $ownedCustomer = Customer::create([
            'code' => 'PO-OWNER',
            'name' => 'Owner',
            'email' => 'owner-po@example.com',
            'created_by' => $owner->id,
        ]);

        $otherCustomer = Customer::create([
            'code' => 'PO-OTHER',
            'name' => 'Other',
            'email' => 'other-po@example.com',
            'created_by' => $other->id,
        ]);

        $this->actingAs($owner)->post(route('purchase-orders.store'), [
            'customer_id' => $ownedCustomer->id,
            'invoice_po_code' => '001',
            'po_date' => '2025-11-01',
            'file' => UploadedFile::fake()->create('po.pdf', 10, 'application/pdf'),
            'items' => [
                ['description' => 'Layanan', 'qty' => 1, 'unit_price' => 100],
            ],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->actingAs($other);
        $otherPurchaseOrder = PurchaseOrder::create([
            'customer_id' => $otherCustomer->id,
            'invoice_po_code' => '002',
            'po_date' => '2025-11-01',
            'file_path' => 'po/other.pdf',
            'file_name' => 'other.pdf',
        ]);

        $this->actingAs($owner)->get(route('purchase-orders.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('purchaseOrders.total', 1)
                ->where('purchaseOrders.data.0.customer_id', $ownedCustomer->id));

        $this->get(route('purchase-orders.show', $otherPurchaseOrder))->assertNotFound();
    }

    public function test_invoice_settings_are_scoped_per_user(): void
    {
        $owner = $this->invoiceUser();
        $other = $this->invoiceUser();

        $values = collect(app(SettingService::class)->allByGroup('invoice'))
            ->mapWithKeys(fn (mixed $value, string $key) => [Str::after($key, 'invoice.') => $value])
            ->all();

        $this->actingAs($owner)->put(route('invoice-settings.update'), array_merge($values, [
            'company_name' => 'Owner Corp',
        ]))->assertSessionHasNoErrors();

        $this->actingAs($other)->put(route('invoice-settings.update'), array_merge($values, [
            'company_name' => 'Other Corp',
        ]))->assertSessionHasNoErrors();

        $this->actingAs($owner)->get(route('invoice-settings.edit'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('settings.company_name', 'Owner Corp'));

        $this->actingAs($other)->get(route('invoice-settings.edit'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('settings.company_name', 'Other Corp'));
    }

    private function invoiceUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('admin-invoice');

        return $user;
    }
}
