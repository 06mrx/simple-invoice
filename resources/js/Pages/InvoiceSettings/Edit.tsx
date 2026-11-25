import RichTextEditor from '@/Components/RichTextEditor';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import { Textarea } from '@/Components/ui/textarea';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, usePage } from '@inertiajs/react';
import { FormEvent } from 'react';

type InvoiceSettings = {
    company_name: string;
    npwp: string;
    address: string;
    city: string;
    phone: string;
    email: string;
    contact_name: string;
    bank_name: string;
    bank_account: string;
    bank_account_name: string;
    tax_rate: string | number;
    generate_day: string | number;
    generate_time: string;
    timezone: string;
    schedule_mode: string;
    email_template: string;
    last_invoice_sequence: string | number;
    last_customer_number: string | number;
};

const stringValue = (value: string | number | undefined) =>
    value === undefined || value === null ? '' : String(value);

export default function InvoiceSettingsEdit({
    settings,
}: {
    settings: InvoiceSettings;
}) {
    const flash = usePage().props.flash;
    const { data, setData, put, processing, errors } = useForm({
        company_name: stringValue(settings.company_name),
        npwp: stringValue(settings.npwp),
        address: stringValue(settings.address),
        city: stringValue(settings.city),
        phone: stringValue(settings.phone),
        email: stringValue(settings.email),
        contact_name: stringValue(settings.contact_name),
        bank_name: stringValue(settings.bank_name),
        bank_account: stringValue(settings.bank_account),
        bank_account_name: stringValue(settings.bank_account_name),
        tax_rate: stringValue(settings.tax_rate),
        generate_day: stringValue(settings.generate_day),
        generate_time: stringValue(settings.generate_time),
        timezone: stringValue(settings.timezone),
        schedule_mode: stringValue(settings.schedule_mode || 'generate_day'),
        email_template: stringValue(settings.email_template),
        last_invoice_sequence: stringValue(settings.last_invoice_sequence),
        last_customer_number: stringValue(settings.last_customer_number),
    });

    const submit = (e: FormEvent) => {
        e.preventDefault();
        put(route('invoice-settings.update'));
    };

    const field = (name: keyof typeof data, label: string, type = 'text') => (
        <div>
            <Label>{label}</Label>
            <Input
                type={type}
                value={data[name]}
                onChange={(e) => setData(name, e.target.value)}
            />
            {errors[name] && (
                <p className="text-destructive text-sm">{errors[name]}</p>
            )}
        </div>
    );

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold">Pengaturan Invoice</h2>
            }
        >
            <Head title="Pengaturan Invoice" />
            <div className="py-6">
                <div className="mx-auto max-w-4xl space-y-4 px-4 sm:px-6 lg:px-8">
                    {flash?.success && (
                        <div className="rounded-md border border-green-200 bg-green-50 p-3 text-sm text-green-700">
                            {flash.success}
                        </div>
                    )}
                    {flash?.info && (
                        <div className="rounded-md border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                            {flash.info}
                        </div>
                    )}
                    {flash?.error && (
                        <div className="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {flash.error}
                        </div>
                    )}
                    <form
                        onSubmit={submit}
                        className="bg-card space-y-5 rounded-md border p-6"
                    >
                        <div>
                            <h3 className="font-semibold">Data Perusahaan</h3>
                            <p className="text-muted-foreground text-sm">
                                Dipakai pada header invoice dan PDF.
                            </p>
                        </div>
                        <div className="grid gap-4 sm:grid-cols-2">
                            {field('company_name', 'Nama Perusahaan')}
                            {field('npwp', 'NPWP')}
                            {field('city', 'Kota')}
                            {field('phone', 'Telepon')}
                            {field('email', 'Email')}
                            {field('contact_name', 'Nama Kontak')}
                        </div>
                        <div>
                            <Label>Alamat</Label>
                            <Textarea
                                value={data.address}
                                onChange={(e) =>
                                    setData('address', e.target.value)
                                }
                            />
                            {errors.address && (
                                <p className="text-destructive text-sm">
                                    {errors.address}
                                </p>
                            )}
                        </div>

                        <div>
                            <h3 className="font-semibold">Rekening Bank</h3>
                        </div>
                        <div className="grid gap-4 sm:grid-cols-3">
                            {field('bank_name', 'Bank')}
                            {field('bank_account', 'No. Rekening')}
                            {field('bank_account_name', 'Atas Nama')}
                        </div>

                        <div>
                            <h3 className="font-semibold">Generasi Invoice</h3>
                            <p className="text-muted-foreground text-sm">
                                Scheduler cek setiap jam; jam dicocokkan dari
                                Jam Generate, menit diabaikan.
                            </p>
                        </div>
                        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            {field('tax_rate', 'Pajak (0 - 1)')}
                            <div>
                                <Label>Mode Penjadwalan</Label>
                                <Select
                                    value={data.schedule_mode}
                                    onValueChange={(value) =>
                                        setData('schedule_mode', value)
                                    }
                                >
                                    <SelectTrigger>
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="generate_day">
                                            Tanggal Setting
                                        </SelectItem>
                                        <SelectItem value="po_day">
                                            Tanggal PO Customer
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                {errors.schedule_mode && (
                                    <p className="text-destructive text-sm">
                                        {errors.schedule_mode}
                                    </p>
                                )}
                            </div>
                            {field('generate_day', 'Tanggal Generate')}
                            {field('generate_time', 'Jam Generate', 'time')}
                            {field('timezone', 'Timezone')}
                        </div>

                        <div>
                            <h3 className="font-semibold">
                                Template Email Invoice
                            </h3>
                            <p className="text-muted-foreground text-sm">
                                Gunakan placeholder untuk menyisipkan data
                                invoice.
                            </p>
                        </div>
                        <div>
                            <Label>Template Email</Label>
                            <RichTextEditor
                                value={data.email_template}
                                onChange={(value) =>
                                    setData('email_template', value)
                                }
                            />
                            {errors.email_template && (
                                <p className="text-destructive text-sm">
                                    {errors.email_template}
                                </p>
                            )}
                        </div>

                        <div>
                            <h3 className="font-semibold">Penomoran Awal</h3>
                            <p className="text-muted-foreground text-sm">
                                Isi angka terakhir dari data manual. Nomor
                                otomatis berikutnya memakai nilai ini + 1.
                            </p>
                        </div>
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div>
                                {field(
                                    'last_invoice_sequence',
                                    'No. Urut Invoice Terakhir',
                                    'number',
                                )}
                                <p className="text-muted-foreground mt-1 text-xs">
                                    Contoh: 1669 → invoice pertama 1670.
                                </p>
                            </div>
                            <div>
                                {field(
                                    'last_customer_number',
                                    'Nomor Customer Terakhir',
                                    'number',
                                )}
                                <p className="text-muted-foreground mt-1 text-xs">
                                    Angka akhir kode MDxxx. Contoh: 12 → MD013.
                                </p>
                            </div>
                        </div>

                        <div className="flex justify-end">
                            <Button type="submit" disabled={processing}>
                                Simpan Pengaturan
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
