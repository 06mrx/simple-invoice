import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import { Checkbox } from '@/Components/ui/checkbox';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';

type Customer = {
    id: string;
    code: string;
    name: string;
    address: string | null;
    email: string;
    email_cc: string | null;
    npwp: string | null;
    phone: string | null;
    bank_name: string | null;
    bank_account: string | null;
    bank_account_name: string | null;
    include_po_attachment?: boolean;
};

export default function CustomersEdit({ customer }: { customer: Customer }) {
    const { data, setData, put, processing, errors } = useForm({
        code: customer.code,
        name: customer.name,
        address: customer.address ?? '',
        email: customer.email,
        email_cc: customer.email_cc ?? '',
        npwp: customer.npwp ?? '',
        phone: customer.phone ?? '',
        bank_name: customer.bank_name ?? '',
        bank_account: customer.bank_account ?? '',
        bank_account_name: customer.bank_account_name ?? '',
        include_po_attachment: customer.include_po_attachment ?? false,
    });

    const submit = (e: FormEvent) => {
        e.preventDefault();
        put(route('customers.update', customer.id));
    };

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold">Edit Customer</h2>}
        >
            <Head title="Edit Customer" />
            <div className="py-6">
                <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                    <form
                        onSubmit={submit}
                        className="bg-card space-y-4 rounded-md border p-6"
                    >
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div>
                                <Label>Kode <span className="text-destructive">*</span></Label>
                                <Input
                                    value={data.code}
                                    onChange={(e) =>
                                        setData('code', e.target.value)
                                    }
                                />
                                <InputError message={errors.code} />
                            </div>
                            <div>
                                <Label>Nama <span className="text-destructive">*</span></Label>
                                <Input
                                    value={data.name}
                                    onChange={(e) =>
                                        setData('name', e.target.value)
                                    }
                                />
                                <InputError message={errors.name} />
                            </div>
                        </div>
                        <div>
                            <Label>Alamat <span className="text-destructive">*</span></Label>
                            <Textarea
                                value={data.address}
                                onChange={(e) =>
                                    setData('address', e.target.value)
                                }
                            />
                            <InputError message={errors.address} />
                        </div>
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div>
                                <Label>Email <span className="text-destructive">*</span></Label>
                                <Input
                                    type="email"
                                    value={data.email}
                                    onChange={(e) =>
                                        setData('email', e.target.value)
                                    }
                                />
                                <InputError message={errors.email} />
                            </div>
                            <div>
                                <Label>Email CC <span className="text-destructive">*</span></Label>
                                <Input
                                    value={data.email_cc}
                                    placeholder="finance@contoh.com; ops@contoh.com"
                                    onChange={(e) =>
                                        setData('email_cc', e.target.value)
                                    }
                                />
                                <p className="text-muted-foreground mt-1 text-xs">
                                    Pisahkan beberapa email dengan titik koma.
                                </p>
                                <InputError message={errors.email_cc} />
                            </div>
                            <div>
                                <Label>Telepon</Label>
                                <Input
                                    value={data.phone}
                                    onChange={(e) =>
                                        setData('phone', e.target.value)
                                    }
                                />
                                <InputError message={errors.phone} />
                            </div>
                            <div>
                                <Label>NPWP</Label>
                                <Input
                                    value={data.npwp}
                                    onChange={(e) =>
                                        setData('npwp', e.target.value)
                                    }
                                />
                                <InputError message={errors.npwp} />
                            </div>
                            <div>
                                <Label>Bank</Label>
                                <Input
                                    value={data.bank_name}
                                    onChange={(e) =>
                                        setData('bank_name', e.target.value)
                                    }
                                />
                                <InputError message={errors.bank_name} />
                            </div>
                            <div>
                                <Label>No. Rekening</Label>
                                <Input
                                    value={data.bank_account}
                                    onChange={(e) =>
                                        setData('bank_account', e.target.value)
                                    }
                                />
                                <InputError message={errors.bank_account} />
                            </div>
                            <div>
                                <Label>Atas Nama</Label>
                                <Input
                                    value={data.bank_account_name}
                                    onChange={(e) =>
                                        setData(
                                            'bank_account_name',
                                            e.target.value,
                                        )
                                    }
                                />
                                <InputError
                                    message={errors.bank_account_name}
                                />
                            </div>
                        </div>
                        <div className="flex items-center gap-2">
                            <Checkbox
                                id="include_po_attachment"
                                checked={data.include_po_attachment}
                                onCheckedChange={(checked) =>
                                    setData(
                                        'include_po_attachment',
                                        checked === true,
                                    )
                                }
                            />
                            <div>
                                <Label htmlFor="include_po_attachment">
                                    Sertakan file PO sebagai lampiran email
                                    invoice
                                </Label>
                                <InputError
                                    message={errors.include_po_attachment}
                                />
                            </div>
                        </div>
                        <div className="flex justify-end gap-2">
                            <Button variant="outline" asChild>
                                <Link href={route('customers.index')}>
                                    Batal
                                </Link>
                            </Button>
                            <Button type="submit" disabled={processing}>
                                Simpan
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
