<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoicePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminSeeder::class);
    }

    public function test_admin_invoice_role_can_access_invoice_pages(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin-invoice');

        $this->actingAs($user);

        $this->get(route('invoices.index'))->assertOk();
        $this->get(route('customers.index'))->assertOk();
        $this->get(route('customers.create'))->assertOk();
        $this->get(route('purchase-orders.index'))->assertOk();
        $this->get(route('purchase-orders.create'))->assertOk();
        $this->get(route('invoice-settings.edit'))->assertOk();
    }

    public function test_plain_user_cannot_access_invoice_pages(): void
    {
        $user = User::where('email', 'dummy@example.com')->first();

        $this->actingAs($user);

        $this->get(route('invoices.index'))->assertForbidden();
        $this->get(route('customers.index'))->assertForbidden();
        $this->get(route('purchase-orders.index'))->assertForbidden();
        $this->get(route('invoice-settings.edit'))->assertForbidden();
    }
}
