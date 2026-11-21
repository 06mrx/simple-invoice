<?php

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\NumberSequence;
use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $lastInvoice = (int) (Invoice::max('sequence') ?: 1669);
        $invoiceNext = NumberSequence::where('key', 'invoice')->value('next_value');

        if ($invoiceNext !== null) {
            $lastInvoice = max($lastInvoice, (int) $invoiceNext - 1);
        }

        $lastCustomer = 0;

        foreach (Customer::query()->pluck('code') as $code) {
            if (preg_match('/(\d+)$/', (string) $code, $matches)) {
                $lastCustomer = max($lastCustomer, (int) $matches[1]);
            }
        }

        $customerNext = NumberSequence::where('key', 'customer')->value('next_value');

        if ($customerNext !== null) {
            $lastCustomer = max($lastCustomer, (int) $customerNext - 1);
        }

        foreach ([
            'invoice.last_invoice_sequence' => $lastInvoice,
            'invoice.last_customer_number' => $lastCustomer,
        ] as $key => $value) {
            Setting::firstOrCreate(
                ['key' => $key],
                [
                    'value' => (string) $value,
                    'type' => 'integer',
                    'group' => 'invoice',
                ]
            );
        }
    }

    public function down(): void
    {
        Setting::whereIn('key', [
            'invoice.last_invoice_sequence',
            'invoice.last_customer_number',
        ])->delete();
    }
};
