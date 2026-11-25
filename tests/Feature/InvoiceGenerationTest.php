<?php

namespace Tests\Feature;

use App\Jobs\GenerateInvoicesJob;
use App\Mail\InvoiceGenerated;
use App\Models\Customer;
use App\Models\InstallationItem;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Services\InvoiceGenerator;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InvoiceGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminSeeder::class);
        Storage::fake('local');
        Mail::fake();
    }

    public function test_generates_invoice_with_installation_tax_and_attachments(): void
    {
        $customer = Customer::create([
            'code' => 'MD001',
            'name' => 'PT Cross Network Indonesia',
            'address' => 'Jl. Test No. 1',
            'email' => 'customer@example.com',
            'phone' => '081234567890',
        ]);

        $purchaseOrder = PurchaseOrder::create([
            'customer_id' => $customer->id,
            'invoice_po_code' => '003',
            'po_date' => '2023-05-16',
            'file_path' => 'po/test.pdf',
            'file_name' => 'po.pdf',
        ]);

        PurchaseOrderItem::create([
            'po_id' => $purchaseOrder->id,
            'description' => 'Layanan Internet',
            'qty' => 2,
            'unit_price' => 100,
            'amount' => 200,
            'sort_order' => 0,
        ]);

        InstallationItem::create([
            'po_id' => $purchaseOrder->id,
            'name' => 'Instalasi',
            'qty' => 2,
            'price' => 25,
            'sort_order' => 0,
        ]);

        $invoice = app(InvoiceGenerator::class)
            ->generateForPurchaseOrder(
                $purchaseOrder,
                Carbon::parse('2025-11-01'),
                true,
            );

        $this->assertSame('0111INV0031670', $invoice->invoice_number);
        $this->assertSame(250.0, (float) $invoice->subtotal);
        $this->assertSame(30.0, (float) $invoice->tax_amount);
        $this->assertSame(280.0, (float) $invoice->total);
        $this->assertCount(2, $invoice->items);
        $this->assertSame('sent', $invoice->status);

        Storage::disk('local')->assertExists($invoice->xlsx_path);
        Storage::disk('local')->assertExists($invoice->pdf_path);
        $this->assertStringStartsWith(
            '%PDF',
            Storage::disk('local')->get($invoice->pdf_path),
        );
        Mail::assertSent(InvoiceGenerated::class);

        $again = app(InvoiceGenerator::class)
            ->generateForPurchaseOrder(
                $purchaseOrder,
                Carbon::parse('2025-11-01'),
                true,
                true,
            );

        $this->assertNotSame($invoice->id, $again->id);
        $this->assertSame('0111INV0031671', $again->invoice_number);
        $this->assertSame(2, Invoice::count());
        Mail::assertSent(InvoiceGenerated::class, 2);
    }

    public function test_generate_for_date_respects_configured_day(): void
    {
        $generator = app(InvoiceGenerator::class);

        $this->assertCount(0, $generator->generateForDate(Carbon::parse('2025-11-02')));
        $this->assertCount(0, $generator->generateForDate(Carbon::parse('2025-11-01')));
    }

    public function test_manual_generate_dispatches_job(): void
    {
        Queue::fake();
        $user = \App\Models\User::query()->firstOrFail();

        $this->actingAs($user)
            ->post(route('invoices.generate'), ['date' => '2025-11-01'])
            ->assertRedirect(route('invoices.index'))
            ->assertSessionHas('info');

        Queue::assertPushed(GenerateInvoicesJob::class);
    }

    public function test_invoice_mail_excludes_xlsx_and_conditionally_includes_po(): void
    {
        $user = \App\Models\User::query()->firstOrFail();
        $this->actingAs($user);

        $customer = Customer::create([
            'code' => 'MD999',
            'name' => 'Cust Mail',
            'email' => 'cust@example.com',
            'include_po_attachment' => false,
            'created_by' => $user->id,
        ]);

        $po = PurchaseOrder::create([
            'customer_id' => $customer->id,
            'invoice_po_code' => 'PO001',
            'po_number' => 'PO001',
            'po_date' => '2025-11-01',
            'subtotal' => 100,
            'tax_rate' => 0.12,
            'tax_amount' => 12,
            'total' => 112,
            'file_path' => 'po/test.pdf',
            'file_name' => 'test.pdf',
            'file_mime' => 'application/pdf',
            'created_by' => $user->id,
        ]);

        Storage::disk('local')->put('po/test.pdf', 'po');

        $invoice = app(InvoiceGenerator::class)->generateForPurchaseOrder($po, Carbon::parse('2025-11-01'), false, true);
        $invoice->refresh();

        $mailable = new InvoiceGenerated($invoice);
        $attachments = collect($mailable->attachments())->map(fn ($a) => $a->as ?? '')->all();
        $this->assertFalse(in_array($invoice->invoice_number.'.xlsx', $attachments, true));
        $this->assertTrue(in_array($invoice->invoice_number.'.pdf', $attachments, true));
        $this->assertFalse(in_array('test.pdf', $attachments, true));

        $customer->update(['include_po_attachment' => true]);
        $invoice->refresh();
        $mailable = new InvoiceGenerated($invoice);
        $attachments = collect($mailable->attachments())->map(fn ($a) => $a->as ?? '')->all();
        $this->assertTrue(in_array('test.pdf', $attachments, true));
    }
}
