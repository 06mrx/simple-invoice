<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class InvoiceSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'invoice.company_name' => ['CV MAKSIMAL DATA', 'string'],
            'invoice.npwp' => ['90.331.291.6-805.000', 'string'],
            'invoice.address' => ['JL KESATUAN LRG 1 NO.6B', 'string'],
            'invoice.city' => ['MAKASSAR, 90144', 'string'],
            'invoice.phone' => ['0852 1119 1990', 'string'],
            'invoice.email' => ['cs@maksimaldata.com', 'string'],
            'invoice.contact_name' => ['MUHAMMAD AKBAR', 'string'],
            'invoice.bank_name' => ['BCA', 'string'],
            'invoice.bank_account' => ['1109777771', 'string'],
            'invoice.bank_account_name' => ['MAKSIMAL DATA', 'string'],
            'invoice.tax_rate' => ['0.12', 'decimal'],
            'invoice.generate_day' => ['1', 'integer'],
            'invoice.generate_time' => ['08:00', 'string'],
            'invoice.timezone' => ['Asia/Makassar', 'string'],
            'invoice.last_invoice_sequence' => ['1669', 'integer'],
            'invoice.last_customer_number' => ['0', 'integer'],
        ];

        foreach ($settings as $key => [$value, $type]) {
            Setting::firstOrCreate(
                ['user_id' => null, 'key' => $key],
                [
                    'value' => $value,
                    'type' => $type,
                    'group' => 'invoice',
                ]
            );
        }
    }
}
