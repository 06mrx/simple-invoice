<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\NumberSequenceService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function __construct(
        protected NumberSequenceService $sequences,
        protected SettingService $settings,
    )
    {
        $this->middleware('permission:view-customer')->only(['index']);
        $this->middleware('permission:create-customer')->only(['create', 'store']);
        $this->middleware('permission:edit-customer')->only(['edit', 'update']);
        $this->middleware('permission:delete-customer')->only(['destroy']);
    }

    public function index(): Response
    {
        $query = Customer::where('created_by', auth()->id());

        if (request('search')) {
            $search = request('search');
            $query->where(function ($customerQuery) use ($search) {
                $customerQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return Inertia::render('Customers/Index', [
            'customers' => $query->orderBy('code')->paginate(request('per_page', 10))->withQueryString(),
            'filters' => [
                'search' => request('search', ''),
                'per_page' => request('per_page', 10),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Customers/Create');
    }

    public function store(Request $request)
    {
        $userId = (string) auth()->id();

        $data = $request->validate([
            'code' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('customers', 'code')->where(fn ($query) => $query->where('created_by', $userId)),
            ],
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('customers', 'email')->where(fn ($query) => $query->where('created_by', $userId)),
            ],
            'email_cc' => ['required', 'string', $this->emailCcRule()],
            'npwp' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:100',
            'bank_account_name' => 'nullable|string|max:100',
            'include_po_attachment' => 'boolean',
        ]);

        $data['include_po_attachment'] = $request->boolean('include_po_attachment');
        $data['email_cc'] = $this->normalizeEmailCc($data['email_cc'] ?? null);

        $lastNumber = (int) $this->settings->get('invoice.last_customer_number', 0, $userId);
        $data['code'] = ($data['code'] ?? null) ?: sprintf('MD%03d', $this->sequences->next('customer', $lastNumber + 1));

        Customer::create($data);

        return redirect()->route('customers.index')
            ->with('success', 'Customer berhasil ditambahkan.');
    }

    public function edit(Customer $customer): Response
    {
        $this->ensureOwned($customer);

        return Inertia::render('Customers/Edit', [
            'customer' => $customer,
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $this->ensureOwned($customer);
        $userId = (string) auth()->id();

        $data = $request->validate([
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('customers', 'code')->where(fn ($query) => $query->where('created_by', $userId))->ignore($customer->id),
            ],
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('customers', 'email')->where(fn ($query) => $query->where('created_by', $userId))->ignore($customer->id),
            ],
            'email_cc' => ['required', 'string', $this->emailCcRule()],
            'npwp' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:100',
            'bank_account_name' => 'nullable|string|max:100',
            'include_po_attachment' => 'boolean',
        ]);

        $data['include_po_attachment'] = $request->boolean('include_po_attachment');
        $data['email_cc'] = $this->normalizeEmailCc($data['email_cc'] ?? null);

        $customer->update($data);

        return redirect()->route('customers.index')
            ->with('success', 'Customer berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $this->ensureOwned($customer);
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer berhasil dihapus.');
    }

    private function ensureOwned(Customer $customer): void
    {
        abort_unless((string) $customer->created_by === (string) auth()->id(), 404);
    }

    private function emailCcRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            $emails = $this->parseEmailCc($value);
            $validator = \Illuminate\Support\Facades\Validator::make(
                ['emails' => $emails],
                ['emails.*' => ['email']]
            );

            if ($validator->fails()) {
                $fail('Format email CC tidak valid. Pisahkan dengan titik koma.');
            }
        };
    }

    private function normalizeEmailCc(?string $value): ?string
    {
        $emails = $this->parseEmailCc($value);

        return $emails === [] ? null : implode('; ', array_values(array_unique($emails)));
    }

    private function parseEmailCc(mixed $value): array
    {
        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        return collect(preg_split('/[;,]+/', $value) ?: [])
            ->map(fn ($email) => trim((string) $email))
            ->filter()
            ->values()
            ->all();
    }
}
