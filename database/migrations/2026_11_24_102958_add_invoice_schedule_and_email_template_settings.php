<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            [
                'key' => 'invoice.schedule_mode',
                'value' => 'generate_day',
                'type' => 'string',
                'group' => 'invoice',
            ],
            [
                'key' => 'invoice.email_template',
                'value' => '<p>Yth. {{customer_name}},</p><p>Invoice {{invoice_number}} untuk periode {{period}} telah dibuat.</p><p>Tanggal: {{invoice_date}}<br>Total: {{total}}</p><p>Terima kasih,<br>{{company_name}}</p>',
                'type' => 'string',
                'group' => 'invoice',
            ],
        ];

        foreach ($defaults as $setting) {
            Setting::query()->firstOrCreate(
                ['user_id' => null, 'key' => $setting['key']],
                $setting
            );
        }
    }

    public function down(): void
    {
        Setting::query()
            ->whereNull('user_id')
            ->whereIn('key', ['invoice.schedule_mode', 'invoice.email_template'])
            ->delete();
    }
};
