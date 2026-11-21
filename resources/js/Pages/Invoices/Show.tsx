import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card } from '@/Components/ui/card';
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
import { Head, Link, router } from '@inertiajs/react';

type InvoiceItem = {
    id: string;
    kind: string;
    description: string;
    qty: string;
    unit_price: string;
    amount: string;
};

type Invoice = {
    id: string;
    invoice_number: string;
    invoice_date: string;
    period: string;
    status: string;
    subtotal: string;
    tax_rate: string;
    tax_amount: string;
    total: string;
    sent_at: string | null;
    xlsx_path: string | null;
    pdf_path: string | null;
    customer: {
        code: string;
        name: string;
        address: string | null;
        email: string;
        phone: string | null;
    };
    purchase_order: {
        invoice_po_code: string;
        file_name: string;
    } | null;
    items: InvoiceItem[];
};

export default function InvoicesShow({ invoice }: { invoice: Invoice }) {
    const { can } = usePermissions();

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold">Detail Invoice</h2>}
        >
            <Head title="Detail Invoice" />
            <div className="py-6">
                <div className="mx-auto max-w-5xl space-y-4 px-4 sm:px-6 lg:px-8">
                    <Card className="space-y-3 p-6">
                        <div className="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <p className="text-muted-foreground text-xs">
                                    Nomor Invoice
                                </p>
                                <p className="text-lg font-semibold">
                                    {invoice.invoice_number}
                                </p>
                            </div>
                            <Badge
                                variant={
                                    invoice.status === 'sent'
                                        ? 'default'
                                        : invoice.status === 'failed'
                                          ? 'destructive'
                                          : 'secondary'
                                }
                            >
                                {invoice.status}
                            </Badge>
                        </div>
                        <div className="grid gap-3 sm:grid-cols-2">
                            <div>
                                <p className="text-muted-foreground text-xs">
                                    Customer
                                </p>
                                <p className="font-medium">
                                    {invoice.customer.code} -{' '}
                                    {invoice.customer.name}
                                </p>
                                <p className="text-sm">
                                    {invoice.customer.address ?? '-'}
                                </p>
                                <p className="text-sm">
                                    {invoice.customer.email} /{' '}
                                    {invoice.customer.phone ?? '-'}
                                </p>
                            </div>
                            <div>
                                <p className="text-muted-foreground text-xs">
                                    Periode
                                </p>
                                <p className="font-medium">{invoice.period}</p>
                                <p className="text-sm">
                                    Tanggal: {invoice.invoice_date}
                                </p>
                                <p className="text-sm">
                                    PO:{' '}
                                    {invoice.purchase_order
                                        ? invoice.purchase_order.invoice_po_code
                                        : '-'}
                                </p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2 pt-2">
                            {invoice.xlsx_path && can('view-invoice') && (
                                <Button variant="outline" size="sm" asChild>
                                    <a
                                        href={route('invoices.download', [
                                            invoice.id,
                                            'xlsx',
                                        ])}
                                    >
                                        Download XLSX
                                    </a>
                                </Button>
                            )}
                            {invoice.pdf_path && can('view-invoice') && (
                                <Button variant="outline" size="sm" asChild>
                                    <a
                                        href={route('invoices.download', [
                                            invoice.id,
                                            'pdf',
                                        ])}
                                    >
                                        Download PDF
                                    </a>
                                </Button>
                            )}
                            {!invoice.sent_at && can('send-invoice') && (
                                <Button
                                    size="sm"
                                    onClick={() => {
                                        if (
                                            confirm(
                                                'Kirim invoice ke ' +
                                                    invoice.customer.email +
                                                    '?',
                                            )
                                        )
                                            router.post(
                                                route(
                                                    'invoices.send',
                                                    invoice.id,
                                                ),
                                            );
                                    }}
                                >
                                    Kirim Email
                                </Button>
                            )}
                        </div>
                    </Card>

                    <Card>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Deskripsi</TableHead>
                                    <TableHead>Jenis</TableHead>
                                    <TableHead className="text-right">
                                        Qty
                                    </TableHead>
                                    <TableHead className="text-right">
                                        Harga Satuan
                                    </TableHead>
                                    <TableHead className="text-right">
                                        Jumlah
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {invoice.items.map((item) => (
                                    <TableRow key={item.id}>
                                        <TableCell>
                                            {item.description}
                                        </TableCell>
                                        <TableCell>{item.kind}</TableCell>
                                        <TableCell className="text-right">
                                            {item.qty}
                                        </TableCell>
                                        <TableCell className="text-right">
                                            {item.unit_price}
                                        </TableCell>
                                        <TableCell className="text-right">
                                            {item.amount}
                                        </TableCell>
                                    </TableRow>
                                ))}
                                <TableRow>
                                    <TableCell
                                        colSpan={4}
                                        className="text-right font-medium"
                                    >
                                        Subtotal
                                    </TableCell>
                                    <TableCell className="text-right">
                                        {invoice.subtotal}
                                    </TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableCell
                                        colSpan={4}
                                        className="text-right font-medium"
                                    >
                                        Pajak ({invoice.tax_rate})
                                    </TableCell>
                                    <TableCell className="text-right">
                                        {invoice.tax_amount}
                                    </TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableCell
                                        colSpan={4}
                                        className="text-right font-semibold"
                                    >
                                        Total
                                    </TableCell>
                                    <TableCell className="text-right font-semibold">
                                        {invoice.total}
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </Card>

                    <Button variant="outline" asChild>
                        <Link href={route('invoices.index')}>Kembali</Link>
                    </Button>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
