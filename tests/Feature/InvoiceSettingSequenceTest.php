<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Setting;
use App\Models\User;
use App\Services\InvoiceGenerator;
use App\Services\SettingService;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InvoiceSettingSequenceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminSeeder::class);
        Storage::fake('local');
        Mail::fake();
    }

    public function test_settings_override_initial_invoice_and_customer_numbers(): void
    {
        $admin = User::where('email', 'admin@template.com')->firstOrFail();
        $values = app(SettingService::class)->allByGroup('invoice', (string) $admin->id);

        $this->actingAs($admin)->put(route('invoice-settings.update'), [
            'company_name' => $values['invoice.company_name'],
            'npwp' => $values['invoice.npwp'],
            'address' => $values['invoice.address'],
            'city' => $values['invoice.city'],
            'phone' => $values['invoice.phone'],
            'email' => $values['invoice.email'],
            'contact_name' => $values['invoice.contact_name'],
            'bank_name' => $values['invoice.bank_name'],
            'bank_account' => $values['invoice.bank_account'],
            'bank_account_name' => $values['invoice.bank_account_name'],
            'tax_rate' => $values['invoice.tax_rate'],
            'generate_day' => $values['invoice.generate_day'],
            'generate_time' => $values['invoice.generate_time'],
            'timezone' => $values['invoice.timezone'],
            'schedule_mode' => $values['invoice.schedule_mode'],
            'email_template' => $values['invoice.email_template'],
            'last_invoice_sequence' => 2000,
            'last_customer_number' => 50,
        ])->assertSessionHasNoErrors();

        $this->assertSame('2000', Setting::where('user_id', (string) $admin->id)->where('key', 'invoice.last_invoice_sequence')->value('value'));
        $this->assertSame('50', Setting::where('user_id', (string) $admin->id)->where('key', 'invoice.last_customer_number')->value('value'));

        $this->actingAs($admin)->post(route('customers.store'), [
            'name' => 'PT Cross Network Indonesia',
            'address' => 'Jl. Test 1',
            'email' => 'customer@example.com',
            'email_cc' => 'cc@example.com',
        ])->assertRedirect(route('customers.index'))->assertSessionHasNoErrors();

        $customer = Customer::where('email', 'customer@example.com')->firstOrFail();

        $this->assertSame('MD051', $customer->code);

        $purchaseOrder = PurchaseOrder::create([
            'customer_id' => $customer->id,
            'invoice_po_code' => '003',
            'po_date' => '2023-05-16',
            'file_path' => 'po/test.pdf',
            'file_name' => 'po.pdf',
            'created_by' => $admin->id,
        ]);

        PurchaseOrderItem::create([
            'po_id' => $purchaseOrder->id,
            'description' => 'Layanan Internet',
            'qty' => 1,
            'unit_price' => 100,
            'amount' => 100,
            'sort_order' => 0,
        ]);

        $invoice = app(InvoiceGenerator::class)
            ->generateForPurchaseOrder($purchaseOrder, Carbon::parse('2025-11-01'));

        $this->assertSame('0111INV0032001', $invoice->invoice_number);
    }
}
