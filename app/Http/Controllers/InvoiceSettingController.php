<?php

namespace App\Http\Controllers;

use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceSettingController extends Controller
{
    public function __construct(protected SettingService $settings)
    {
        $this->middleware('permission:view-invoicesetting')->only(['edit']);
        $this->middleware('permission:edit-invoicesetting')->only(['update']);
    }

    public function edit(): Response
    {
        $values = collect($this->settings->allByGroup('invoice'))
            ->mapWithKeys(fn (mixed $value, string $key) => [Str::after($key, 'invoice.') => $value])
            ->all();

        return Inertia::render('InvoiceSettings/Edit', [
            'settings' => $values,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'npwp' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'contact_name' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:100',
            'bank_account_name' => 'nullable|string|max:255',
            'tax_rate' => 'required|numeric|min:0|max:1',
            'generate_day' => 'required|integer|min:1|max:28',
            'generate_time' => ['required', 'date_format:H:i'],
            'timezone' => ['required', 'timezone'],
            'schedule_mode' => ['required', 'in:generate_day,po_day'],
            'email_template' => 'required|string|max:10000',
            'last_invoice_sequence' => 'required|integer|min:0|max:9999999999',
            'last_customer_number' => 'required|integer|min:0|max:9999999999',
        ]);


        $values = collect($data)
            ->mapWithKeys(fn (mixed $value, string $key) => ['invoice.'.$key => $value])
            ->all();

        $this->settings->updateMany($values, 'invoice');

        return redirect()->route('invoice-settings.edit')
            ->with('success', 'Pengaturan invoice berhasil disimpan.');
    }
}
