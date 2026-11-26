<?php

namespace Tests\Feature;

use App\Mail\InvoiceGenerated;
use App\Models\Customer;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Services\InvoiceGenerator;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomerEmailCcTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminSeeder::class);
    }

    public function test_customer_email_cc_must_be_valid_emails(): void
    {
        $user = User::query()->firstOrFail();

        $this->actingAs($user)
            ->post(route('customers.store'), [
                'name' => 'PT CC Invalid',
                'address' => 'Jl. Test',
                'email' => 'valid@example.com',
                'email_cc' => 'ok@example.com;not-an-email',
            ])
            ->assertSessionHasErrors('email_cc');
    }

    public function test_invoice_mail_is_sent_with_cc_addresses(): void
    {
        Storage::fake('local');
        Mail::fake();
        $user = User::query()->firstOrFail();
        $this->actingAs($user);

        $customer = Customer::create([
            'code' => 'MDCC1',
            'name' => 'PT CC',
            'email' => 'main@example.com',
            'email_cc' => 'finance@example.com; ops@example.com;MAIN@example.com',
            'created_by' => $user->id,
        ]);

        $po = PurchaseOrder::create([
            'customer_id' => $customer->id,
            'invoice_po_code' => 'POCC1',
            'po_number' => 'POCC1',
            'po_date' => '2025-11-01',
            'subtotal' => 100,
            'tax_rate' => 0.12,
            'tax_amount' => 12,
            'total' => 112,
            'file_path' => 'po/test.pdf',
            'file_name' => 'test.pdf',
            'created_by' => $user->id,
        ]);

        app(InvoiceGenerator::class)->generateForPurchaseOrder($po, Carbon::parse('2025-11-01'), false, true);

        Mail::assertSent(InvoiceGenerated::class, function (InvoiceGenerated $mail) {
            return $mail->hasTo('main@example.com')
                && $mail->hasCc('finance@example.com')
                && $mail->hasCc('ops@example.com')
                && ! in_array('main@example.com', collect($mail->cc)->pluck('address')->map(fn ($a) => strtolower($a))->all(), true);
        });
    }
}
