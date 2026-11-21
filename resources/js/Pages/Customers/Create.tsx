import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';

export default function CustomersCreate() {
    const { data, setData, post, processing, errors } = useForm({
        code: '',
        name: '',
        address: '',
        email: '',
        npwp: '',
        phone: '',
        bank_name: '',
        bank_account: '',
        bank_account_name: '',
    });

    const submit = (e: FormEvent) => {
        e.preventDefault();
        post(route('customers.store'));
    };

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold">Tambah Customer</h2>}
        >
            <Head title="Tambah Customer" />
            <div className="py-6">
                <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                    <form
                        onSubmit={submit}
                        className="bg-card space-y-4 rounded-md border p-6"
                    >
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div>
                                <Label>Kode</Label>
                                <Input
                                    placeholder="Kosongkan untuk otomatis"
                                    value={data.code}
                                    onChange={(e) =>
                                        setData('code', e.target.value)
                                    }
                                />
                                {errors.code && (
                                    <p className="text-destructive text-sm">
                                        {errors.code}
                                    </p>
                                )}
                            </div>
                            <div>
                                <Label>Nama</Label>
                                <Input
                                    value={data.name}
                                    onChange={(e) =>
                                        setData('name', e.target.value)
                                    }
                                />
                                {errors.name && (
                                    <p className="text-destructive text-sm">
                                        {errors.name}
                                    </p>
                                )}
                            </div>
                        </div>
                        <div>
                            <Label>Alamat</Label>
                            <Textarea
                                value={data.address}
                                onChange={(e) =>
                                    setData('address', e.target.value)
                                }
                            />
                        </div>
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div>
                                <Label>Email</Label>
                                <Input
                                    type="email"
                                    value={data.email}
                                    onChange={(e) =>
                                        setData('email', e.target.value)
                                    }
                                />
                                {errors.email && (
                                    <p className="text-destructive text-sm">
                                        {errors.email}
                                    </p>
                                )}
                            </div>
                            <div>
                                <Label>Telepon</Label>
                                <Input
                                    value={data.phone}
                                    onChange={(e) =>
                                        setData('phone', e.target.value)
                                    }
                                />
                            </div>
                            <div>
                                <Label>NPWP</Label>
                                <Input
                                    value={data.npwp}
                                    onChange={(e) =>
                                        setData('npwp', e.target.value)
                                    }
                                />
                            </div>
                            <div>
                                <Label>Bank</Label>
                                <Input
                                    value={data.bank_name}
                                    onChange={(e) =>
                                        setData('bank_name', e.target.value)
                                    }
                                />
                            </div>
                            <div>
                                <Label>No. Rekening</Label>
                                <Input
                                    value={data.bank_account}
                                    onChange={(e) =>
                                        setData('bank_account', e.target.value)
                                    }
                                />
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
