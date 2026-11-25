import ConfirmDialog from '@/Components/ConfirmDialog';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card } from '@/Components/ui/card';
import { Checkbox } from '@/Components/ui/checkbox';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
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
import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import { FormEvent, useState } from 'react';

type Invoice = {
    id: string;
    invoice_number: string;
    invoice_date: string;
    period: string;
    status: string;
    total: string;
    sent_at: string | null;
    xlsx_path: string | null;
    pdf_path: string | null;
    customer: { code: string; name: string };
    purchase_order: { invoice_po_code: string } | null;
};

type PaginatorLink = { url: string | null; label: string; active: boolean };
type Paginator<T> = { data: T[]; links: PaginatorLink[] };

type Props = {
    invoices: Paginator<Invoice>;
    purchaseOrders: { id: string; label: string }[];
    timezone: string;
    filters: { search: string; status: string; per_page: number };
};

const statusVariant = (status: string) => {
    if (status === 'sent') return 'default' as const;
    if (status === 'failed') return 'destructive' as const;
    return 'secondary' as const;
};

export default function InvoicesIndex({
    invoices,
    purchaseOrders,
    timezone,
    filters,
}: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [status, setStatus] = useState(filters.status ?? '');
    const [deleteTarget, setDeleteTarget] = useState<Invoice | null>(null);
    const flash = usePage().props.flash;
    const { can } = usePermissions();
    const generate = useForm({
        date: new Date().toISOString().slice(0, 10),
        purchase_order_id: '',
        include_installation: false,
    });

    const submitFilter = (e?: FormEvent) => {
        e?.preventDefault();
        router.get(
            route('invoices.index'),
            { search, status },
            { preserveState: true, replace: true },
        );
    };

    const submitGenerate = (e: FormEvent) => {
        e.preventDefault();
        generate.post(route('invoices.generate'), {
            preserveScroll: true,
            onSuccess: () => generate.reset('purchase_order_id'),
        });
    };

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold">Invoice</h2>}
        >
            <Head title="Invoice" />
            <div className="py-6">
                <div className="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
                    {flash?.success && (
                        <div className="rounded-md border border-green-200 bg-green-50 p-3 text-sm text-green-700">
                            {flash.success}
                        </div>
                    )}
                    {flash?.info && (
                        <div className="rounded-md border border-blue-200 bg-blue-50 p-3 text-sm text-blue-700">
                            {flash.info}
                        </div>
                    )}
                    {flash?.error && (
                        <div className="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                            {flash.error}
                        </div>
                    )}

                    {can('create-invoice') && (
                        <Card className="p-4">
                            <form
                                onSubmit={submitGenerate}
                                className="flex flex-wrap items-end gap-3"
                            >
                                <div>
                                    <Label>Tanggal Generate</Label>
                                    <Input
                                        type="date"
                                        value={generate.data.date}
                                        onChange={(e) =>
                                            generate.setData(
                                                'date',
                                                e.target.value,
                                            )
                                        }
                                    />
                                </div>
                                <div className="min-w-64">
                                    <Label>PO Tertentu (opsional)</Label>
                                    <select
                                        className="border-input bg-background h-10 w-full rounded-md border px-3 text-sm"
                                        value={generate.data.purchase_order_id}
                                        onChange={(e) =>
                                            generate.setData(
                                                'purchase_order_id',
                                                e.target.value,
                                            )
                                        }
                                    >
                                        <option value="">Semua PO</option>
                                        {purchaseOrders.map((po) => (
                                            <option key={po.id} value={po.id}>
                                                {po.label}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <label className="flex items-center gap-2 pb-2 text-sm">
                                    <Checkbox
                                        checked={
                                            generate.data.include_installation
                                        }
                                        onCheckedChange={(checked) =>
                                            generate.setData(
                                                'include_installation',
                                                checked === true,
                                            )
                                        }
                                    />
                                    Sertakan biaya instalasi
                                </label>
                                <Button
                                    type="submit"
                                    disabled={generate.processing}
                                >
                                    Generate Invoice
                                </Button>
                                <span className="text-muted-foreground text-xs">
                                    Timezone: {timezone}
                                </span>
                            </form>
                        </Card>
                    )}

                    <form
                        onSubmit={submitFilter}
                        className="flex flex-wrap gap-2"
                    >
                        <Input
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Cari nomor/customer..."
                            className="w-72"
                        />
                        <select
                            className="border-input bg-background h-10 rounded-md border px-3 text-sm"
                            value={status}
                            onChange={(e) => setStatus(e.target.value)}
                        >
                            <option value="">Semua status</option>
                            <option value="generated">Generated</option>
                            <option value="sent">Sent</option>
                            <option value="failed">Failed</option>
                        </select>
                        <Button type="submit">Cari</Button>
                    </form>

                    <Card>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>No. Invoice</TableHead>
                                    <TableHead>Customer</TableHead>
                                    <TableHead>Periode</TableHead>
                                    <TableHead>Kode PO</TableHead>
                                    <TableHead>Total</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead className="text-right">
                                        Aksi
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {invoices.data.length === 0 ? (
                                    <TableRow>
                                        <TableCell
                                            colSpan={7}
                                            className="text-muted-foreground text-center"
                                        >
                                            Tidak ada data
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    invoices.data.map((invoice) => (
                                        <TableRow key={invoice.id}>
                                            <TableCell>
                                                {invoice.invoice_number}
                                            </TableCell>
                                            <TableCell>
                                                {invoice.customer.code} -{' '}
                                                {invoice.customer.name}
                                            </TableCell>
                                            <TableCell>
                                                {formatDate(invoice.period, {
                                                    month: 'long',
                                                    year: 'numeric',
                                                })}
                                            </TableCell>
                                            <TableCell>
                                                {invoice.purchase_order
                                                    ?.invoice_po_code ?? '-'}
                                            </TableCell>
                                            <TableCell>
                                                {invoice.total}
                                            </TableCell>
                                            <TableCell>
                                                <Badge
                                                    variant={statusVariant(
                                                        invoice.status,
                                                    )}
                                                >
                                                    {invoice.status}
                                                </Badge>
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <div className="flex justify-end gap-2">
                                                    {can('view-invoice') && (
                                                        <>
                                                            <Button
                                                                variant="outline"
                                                                size="sm"
                                                                asChild
                                                            >
                                                                <Link
                                                                    href={route(
                                                                        'invoices.show',
                                                                        invoice.id,
                                                                    )}
                                                                >
                                                                    Detail
                                                                </Link>
                                                            </Button>
                                                            {invoice.xlsx_path && (
                                                                <>
                                                                    <a
                                                                        className="text-primary hover:underline"
                                                                        href={route(
                                                                            'invoices.download',
                                                                            [
                                                                                invoice.id,
                                                                                'xlsx',
                                                                            ],
                                                                        )}
                                                                    >
                                                                        XLSX
                                                                    </a>
                                                                    <a
                                                                        className="text-primary hover:underline"
                                                                        href={route(
                                                                            'invoices.download',
                                                                            [
                                                                                invoice.id,
                                                                                'pdf',
                                                                            ],
                                                                        )}
                                                                    >
                                                                        PDF
                                                                    </a>
                                                                </>
                                                            )}
                                                        </>
                                                    )}
                                                    {can('delete-invoice') && (
                                                        <Button
                                                            variant="destructive"
                                                            size="sm"
                                                            onClick={() =>
                                                                setDeleteTarget(
                                                                    invoice,
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
                        {invoices.links.map((link, i) => (
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
                title="Hapus Invoice"
                description={
                    deleteTarget
                        ? `Hapus invoice ${deleteTarget.invoice_number}?`
                        : undefined
                }
                confirmLabel="Hapus"
                destructive
                onClose={() => setDeleteTarget(null)}
                onConfirm={() => {
                    if (!deleteTarget) return;
                    router.delete(route('invoices.destroy', deleteTarget.id), {
                        onFinish: () => setDeleteTarget(null),
                    });
                }}
            />
        </AuthenticatedLayout>
    );
}
