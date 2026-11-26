<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerPoRequiredFieldsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminSeeder::class);
    }

    public function test_customer_required_fields_are_validated(): void
    {
        $user = User::query()->firstOrFail();

        $this->actingAs($user)
            ->post(route('customers.store'), [])
            ->assertSessionHasErrors(['name', 'address', 'email', 'email_cc']);
    }

    public function test_purchase_order_required_fields_are_validated_and_file_optional(): void
    {
        $user = User::query()->firstOrFail();
        $customer = Customer::create([
            'code' => 'C1',
            'name' => 'PT Test',
            'address' => 'Alamat',
            'email' => 'test@example.com',
            'email_cc' => 'cc@example.com',
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)
            ->post(route('purchase-orders.store'), [])
            ->assertSessionHasErrors(['customer_id', 'invoice_po_code', 'po_date', 'items']);

        $this->actingAs($user)
            ->post(route('purchase-orders.store'), [
                'customer_id' => $customer->id,
                'invoice_po_code' => '001',
                'po_date' => '2026-11-01',
                'items' => [
                    ['description' => 'Layanan', 'qty' => 1, 'unit_price' => 100],
                ],
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
    }
}
