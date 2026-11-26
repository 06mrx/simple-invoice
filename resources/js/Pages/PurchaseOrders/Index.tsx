import ConfirmDialog from '@/Components/ConfirmDialog';
import { Button } from '@/Components/ui/button';
import { Card } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { usePermissions } from '@/hooks/use-permissions';
import { formatDate } from '@/lib/utils';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { FormEvent, useState } from 'react';

type CustomerOption = { id: string; code: string; name: string };
type PurchaseOrder = {
    id: string;
    invoice_po_code: string;
    po_date: string;
    file_name: string;
    customer: CustomerOption;
};

type PaginatorLink = { url: string | null; label: string; active: boolean };
type Paginator<T> = { data: T[]; links: PaginatorLink[] };

type Props = {
    purchaseOrders: Paginator<PurchaseOrder>;
    customers: CustomerOption[];
    filters: { search: string; customer_id: string };
};

export default function PurchaseOrdersIndex({
    purchaseOrders,
    customers,
    filters,
}: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [customerId, setCustomerId] = useState(filters.customer_id ?? '');
    const [deleteTarget, setDeleteTarget] = useState<PurchaseOrder | null>(
        null,
    );
    const flash = usePage().props.flash;
    const { can } = usePermissions();

    const submit = (e?: FormEvent) => {
        e?.preventDefault();
        router.get(
            route('purchase-orders.index'),
            { search, customer_id: customerId },
            { preserveState: true, replace: true },
        );
    };

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold">Purchase Order</h2>}
        >
            <Head title="Purchase Order" />
            <div className="py-6">
                <div className="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
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
                    <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <form
                            onSubmit={submit}
                            className="flex flex-wrap gap-2"
                        >
                            <Input
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Cari kode invoice/file..."
                                className="w-56"
                            />
                            <select
                                className="border-input bg-background h-10 rounded-md border px-3 text-sm"
                                value={customerId}
                                onChange={(e) => setCustomerId(e.target.value)}
                            >
                                <option value="">Semua customer</option>
                                {customers.map((customer) => (
                                    <option
                                        key={customer.id}
                                        value={customer.id}
                                    >
                                        {customer.code} - {customer.name}
                                    </option>
                                ))}
                            </select>
                            <Button type="submit">Cari</Button>
                        </form>
                        {can('create-purchaseorder') && (
                            <Button asChild>
                                <Link href={route('purchase-orders.create')}>
                                    Tambah PO
                                </Link>
                            </Button>
                        )}
                    </div>

                    <Card>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Customer</TableHead>
                                    <TableHead>Kode Invoice</TableHead>
                                    <TableHead>Tanggal</TableHead>
                                    <TableHead>File</TableHead>
                                    <TableHead className="text-right">
                                        Aksi
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {purchaseOrders.data.length === 0 ? (
                                    <TableRow>
                                        <TableCell
                                            colSpan={5}
                                            className="text-muted-foreground text-center"
                                        >
                                            Tidak ada data
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    purchaseOrders.data.map((po) => (
                                        <TableRow key={po.id}>
                                            <TableCell>
                                                {po.customer.code} -{' '}
                                                {po.customer.name}
                                            </TableCell>
                                            <TableCell>
                                                {po.invoice_po_code}
                                            </TableCell>
                                            <TableCell>
                                                {formatDate(po.po_date)}
                                            </TableCell>
                                            <TableCell>
                                                <a
                                                    className="text-primary hover:underline"
                                                    href={route(
                                                        'purchase-orders.download',
                                                        po.id,
                                                    )}
                                                >
                                                    {po.file_name}
                                                </a>
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <div className="flex justify-end gap-2">
                                                    {can(
                                                        'view-purchaseorder',
                                                    ) && (
                                                        <Button
                                                            variant="outline"
                                                            size="sm"
                                                            asChild
                                                        >
                                                            <Link
                                                                href={route(
                                                                    'purchase-orders.show',
                                                                    po.id,
                                                                )}
                                                            >
                                                                Detail
                                                            </Link>
                                                        </Button>
                                                    )}
                                                    {can(
                                                        'edit-purchaseorder',
                                                    ) && (
                                                        <Button
                                                            variant="outline"
                                                            size="sm"
                                                            asChild
                                                        >
                                                            <Link
                                                                href={route(
                                                                    'purchase-orders.edit',
                                                                    po.id,
                                                                )}
                                                            >
                                                                Edit
                                                            </Link>
                                                        </Button>
                                                    )}
                                                    {can(
                                                        'delete-purchaseorder',
                                                    ) && (
                                                        <Button
                                                            variant="destructive"
                                                            size="sm"
                                                            onClick={() =>
                                                                setDeleteTarget(
                                                                    po,
                                                                )
                                                            }
                                                        >
                                                            Hapus
                                                        </Button>
                                                    )}
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    ))
                                )}
                            </TableBody>
                        </Table>
                    </Card>

                    <div className="flex flex-wrap gap-1">
                        {purchaseOrders.links.map((link, i) => (
                            <Button
                                key={i}
                                variant={link.active ? 'default' : 'outline'}
                                size="sm"
                                disabled={!link.url}
                                asChild={!!link.url}
                            >
                                {link.url ? (
                                    <Link
                                        href={link.url}
                                        dangerouslySetInnerHTML={{
                                            __html: link.label,
                                        }}
                                    />
                                ) : (
                                    <span
                                        dangerouslySetInnerHTML={{
                                            __html: link.label,
                                        }}
                                    />
                                )}
                            </Button>
                        ))}
                    </div>
                </div>
            </div>
            <ConfirmDialog
                open={!!deleteTarget}
                title="Hapus PO"
                description={
                    deleteTarget
                        ? `Hapus PO ${deleteTarget.invoice_po_code}?`
                        : undefined
                }
                confirmLabel="Hapus"
                destructive
                onClose={() => setDeleteTarget(null)}
                onConfirm={() => {
                    if (!deleteTarget) return;
                    router.delete(
                        route('purchase-orders.destroy', deleteTarget.id),
                        { onFinish: () => setDeleteTarget(null) },
                    );
                }}
            />
        </AuthenticatedLayout>
    );
}
