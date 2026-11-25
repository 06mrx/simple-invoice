import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';

type CustomerOption = { id: string; code: string; name: string };
type Item = {
    description: string;
    qty: string;
    unit_price: string;
};
type InstallationItem = {
    name: string;
    qty: string;
    price: string;
};
type PurchaseOrderItem = Item & { id: string; amount: string };
type PurchaseOrderInstallationItem = InstallationItem & { id: string };
type PurchaseOrder = {
    id: string;
    customer_id: string;
    invoice_po_code: string;
    po_date: string;
    file_name: string;
    notes: string | null;
    items: PurchaseOrderItem[];
    installation_items: PurchaseOrderInstallationItem[];
};

export default function PurchaseOrdersEdit({
    purchaseOrder,
    customers,
}: {
    purchaseOrder: PurchaseOrder;
    customers: CustomerOption[];
}) {
    const { data, setData, put, processing, errors } = useForm({
        customer_id: purchaseOrder.customer_id,
        invoice_po_code: purchaseOrder.invoice_po_code,
        po_date: purchaseOrder.po_date?.slice(0, 10) ?? '',
        file: null as File | null,
        notes: purchaseOrder.notes ?? '',
        items: purchaseOrder.items.map((item) => ({
            description: item.description,
            qty: item.qty,
            unit_price: item.unit_price,
        })) as Item[],
        installation_items: purchaseOrder.installation_items.map((item) => ({
            name: item.name,
            qty: item.qty,
            price: item.price,
        })) as InstallationItem[],
    });

    const updateItem = (index: number, field: keyof Item, value: string) => {
        const items = data.items.map((item, i) =>
            i === index ? { ...item, [field]: value } : item,
        );
        setData('items', items);
    };

    const updateInstallationItem = (
        index: number,
        field: keyof InstallationItem,
        value: string,
    ) => {
        const items = data.installation_items.map((item, i) =>
            i === index ? { ...item, [field]: value } : item,
        );
        setData('installation_items', items);
    };

    const submit = (e: FormEvent) => {
        e.preventDefault();
        put(route('purchase-orders.update', purchaseOrder.id));
    };

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold">Edit PO</h2>}
        >
            <Head title="Edit PO" />
            <div className="py-6">
                <div className="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                    <form
                        onSubmit={submit}
                        className="bg-card space-y-4 rounded-md border p-6"
                    >
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div>
                                <Label>Customer</Label>
                                <select
                                    className="border-input bg-background h-10 w-full rounded-md border px-3 text-sm"
                                    value={data.customer_id}
                                    onChange={(e) =>
                                        setData('customer_id', e.target.value)
                                    }
                                >
                                    {customers.map((customer) => (
                                        <option
                                            key={customer.id}
                                            value={customer.id}
                                        >
                                            {customer.code} - {customer.name}
                                        </option>
                                    ))}
                                </select>
                                {errors.customer_id && (
                                    <p className="text-destructive text-sm">
                                        {errors.customer_id}
                                    </p>
                                )}
                            </div>
                            <div>
                                <Label>Kode Invoice (3 digit)</Label>
                                <Input
                                    maxLength={3}
                                    value={data.invoice_po_code}
                                    onChange={(e) =>
                                        setData(
                                            'invoice_po_code',
                                            e.target.value,
                                        )
                                    }
                                />
                                {errors.invoice_po_code && (
                                    <p className="text-destructive text-sm">
                                        {errors.invoice_po_code}
                                    </p>
                                )}
                            </div>
                            <div>
                                <Label>Tanggal PO</Label>
                                <Input
                                    type="date"
                                    value={data.po_date}
                                    onChange={(e) =>
                                        setData('po_date', e.target.value)
                                    }
                                />
                                {errors.po_date && (
                                    <p className="text-destructive text-sm">
                                        {errors.po_date}
                                    </p>
                                )}
                            </div>
                            <div className="sm:col-span-2">
                                <Label>File PO (PDF)</Label>
                                <p className="text-muted-foreground mb-1 text-xs">
                                    Kosongkan untuk mempertahankan{' '}
                                    {purchaseOrder.file_name}
                                </p>
                                <Input
                                    type="file"
                                    accept="application/pdf"
                                    onChange={(e) =>
                                        setData(
                                            'file',
                                            e.target.files?.[0] ?? null,
                                        )
                                    }
                                />
                                {errors.file && (
                                    <p className="text-destructive text-sm">
                                        {errors.file}
                                    </p>
                                )}
                            </div>
                        </div>

                        <div className="space-y-2">
                            <div className="flex items-center justify-between">
                                <Label>Item PO</Label>
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    onClick={() =>
                                        setData('items', [
                                            ...data.items,
                                            {
                                                description: '',
                                                qty: '1',
                                                unit_price: '0',
                                            },
                                        ])
                                    }
                                >
                                    Tambah Item
                                </Button>
                            </div>
                            {data.items.map((item, index) => (
                                <div
                                    key={index}
                                    className="grid gap-2 rounded-md border p-3 sm:grid-cols-[1fr_80px_120px_120px_auto]"
                                >
                                    <Input
                                        placeholder="Deskripsi"
                                        value={item.description}
                                        onChange={(e) =>
                                            updateItem(
                                                index,
                                                'description',
                                                e.target.value,
                                            )
                                        }
                                    />
                                    <Input
                                        type="number"
                                        step="0.01"
                                        placeholder="Qty"
                                        value={item.qty}
                                        onChange={(e) =>
                                            updateItem(
                                                index,
                                                'qty',
                                                e.target.value,
                                            )
                                        }
                                    />
                                    <Input
                                        type="number"
                                        step="0.01"
                                        placeholder="Harga"
                                        value={item.unit_price}
                                        onChange={(e) =>
                                            updateItem(
                                                index,
                                                'unit_price',
                                                e.target.value,
                                            )
                                        }
                                    />
                                    <div className="flex h-10 items-center text-sm">
                                        {(
                                            Number(item.qty) *
                                            Number(item.unit_price)
                                        ).toFixed(2)}
                                    </div>
                                    <Button
                                        type="button"
                                        variant="destructive"
                                        size="sm"
                                        disabled={data.items.length === 1}
                                        onClick={() =>
                                            setData(
                                                'items',
                                                data.items.filter(
                                                    (_, i) => i !== index,
                                                ),
                                            )
                                        }
                                    >
                                        Hapus
                                    </Button>
                                </div>
                            ))}
                            {errors.items && (
                                <p className="text-destructive text-sm">
                                    {errors.items}
                                </p>
                            )}
                        </div>

                        <div className="space-y-2">
                            <div className="flex items-center justify-between">
                                <Label>Pekerjaan Instalasi</Label>
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    onClick={() =>
                                        setData('installation_items', [
                                            ...data.installation_items,
                                            {
                                                name: 'Biaya Instalasi',
                                                qty: '1',
                                                price: '0',
                                            },
                                        ])
                                    }
                                >
                                    Tambah Instalasi
                                </Button>
                            </div>
                            {data.installation_items.length === 0 && (
                                <p className="text-muted-foreground text-xs">
                                    Tidak ada pekerjaan instalasi.
                                </p>
                            )}
                            {data.installation_items.map((item, index) => (
                                <div
                                    key={index}
                                    className="grid gap-2 rounded-md border p-3 sm:grid-cols-[1fr_80px_120px_120px_auto]"
                                >
                                    <Input
                                        placeholder="Nama pekerjaan"
                                        value={item.name}
                                        onChange={(e) =>
                                            updateInstallationItem(
                                                index,
                                                'name',
                                                e.target.value,
                                            )
                                        }
                                    />
                                    <Input
                                        type="number"
                                        step="0.01"
                                        placeholder="Qty"
                                        value={item.qty}
                                        onChange={(e) =>
                                            updateInstallationItem(
                                                index,
                                                'qty',
                                                e.target.value,
                                            )
                                        }
                                    />
                                    <Input
                                        type="number"
                                        step="0.01"
                                        placeholder="Harga"
                                        value={item.price}
                                        onChange={(e) =>
                                            updateInstallationItem(
                                                index,
                                                'price',
                                                e.target.value,
                                            )
                                        }
                                    />
                                    <div className="flex h-10 items-center text-sm">
                                        {(
                                            Number(item.qty) *
                                            Number(item.price)
                                        ).toFixed(2)}
                                    </div>
                                    <Button
                                        type="button"
                                        variant="destructive"
                                        size="sm"
                                        onClick={() =>
                                            setData(
                                                'installation_items',
                                                data.installation_items.filter(
                                                    (_, i) => i !== index,
                                                ),
                                            )
                                        }
                                    >
                                        Hapus
                                    </Button>
                                </div>
                            ))}
                            {errors.installation_items && (
                                <p className="text-destructive text-sm">
                                    {errors.installation_items}
                                </p>
                            )}
                        </div>

                        <div>
                            <Label>Catatan</Label>
                            <Textarea
                                value={data.notes}
                                onChange={(e) =>
                                    setData('notes', e.target.value)
                                }
                            />
                        </div>

                        <div className="flex justify-end gap-2">
                            <Button variant="outline" asChild>
                                <Link href={route('purchase-orders.index')}>
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
