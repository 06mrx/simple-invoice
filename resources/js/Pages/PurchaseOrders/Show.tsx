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
import { formatDate } from '@/lib/utils';
import { Head, Link } from '@inertiajs/react';

type PurchaseOrderItem = {
    id: string;
    description: string;
    qty: string;
    unit_price: string;
    amount: string;
};

type InstallationItem = {
    id: string;
    name: string;
    qty: string;
    price: string;
};

type PurchaseOrder = {
    id: string;
    invoice_po_code: string;
    po_date: string;
    file_name: string;
    notes: string | null;
    customer: { code: string; name: string; email: string };
    items: PurchaseOrderItem[];
    installation_items: InstallationItem[];
};

export default function PurchaseOrdersShow({
    purchaseOrder,
}: {
    purchaseOrder: PurchaseOrder;
}) {
    const total = purchaseOrder.items.reduce(
        (sum, item) => sum + Number(item.amount),
        0,
    );

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold">Detail PO</h2>}
        >
            <Head title="Detail PO" />
            <div className="py-6">
                <div className="mx-auto max-w-5xl space-y-4 px-4 sm:px-6 lg:px-8">
                    <Card className="space-y-3 p-6">
                        <div className="grid gap-3 sm:grid-cols-2">
                            <div>
                                <p className="text-muted-foreground text-xs">
                                    Kode Invoice
                                </p>
                                <p className="font-medium">
                                    {purchaseOrder.invoice_po_code}
                                </p>
                            </div>
                            <div>
                                <p className="text-muted-foreground text-xs">
                                    Customer
                                </p>
                                <p className="font-medium">
                                    {purchaseOrder.customer.code} -{' '}
                                    {purchaseOrder.customer.name}
                                </p>
                            </div>
                            <div>
                                <p className="text-muted-foreground text-xs">
                                    Tanggal
                                </p>
                                <p className="font-medium">
                                    {formatDate(purchaseOrder.po_date)}
                                </p>
                            </div>
                            <div>
                                <p className="text-muted-foreground text-xs">
                                    File PO
                                </p>
                                <a
                                    className="text-primary hover:underline"
                                    href={route(
                                        'purchase-orders.download',
                                        purchaseOrder.id,
                                    )}
                                >
                                    {purchaseOrder.file_name}
                                </a>
                            </div>
                        </div>
                        {purchaseOrder.notes && (
                            <p className="text-sm">{purchaseOrder.notes}</p>
                        )}
                    </Card>

                    <Card>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Deskripsi</TableHead>
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
                                {purchaseOrder.items.map((item) => (
                                    <TableRow key={item.id}>
                                        <TableCell>
                                            {item.description}
                                        </TableCell>
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
                                        colSpan={3}
                                        className="text-right font-medium"
                                    >
                                        Subtotal
                                    </TableCell>
                                    <TableCell className="text-right font-medium">
                                        {total.toFixed(2)}
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </Card>

                    {purchaseOrder.installation_items.length > 0 && (
                        <Card>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>
                                            Pekerjaan Instalasi
                                        </TableHead>
                                        <TableHead className="text-right">
                                            Qty
                                        </TableHead>
                                        <TableHead className="text-right">
                                            Harga
                                        </TableHead>
                                        <TableHead className="text-right">
                                            Jumlah
                                        </TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {purchaseOrder.installation_items.map(
                                        (item) => (
                                            <TableRow key={item.id}>
                                                <TableCell>
                                                    {item.name}
                                                </TableCell>
                                                <TableCell className="text-right">
                                                    {item.qty}
                                                </TableCell>
                                                <TableCell className="text-right">
                                                    {item.price}
                                                </TableCell>
                                                <TableCell className="text-right">
                                                    {(
                                                        Number(item.qty) *
                                                        Number(item.price)
                                                    ).toFixed(2)}
                                                </TableCell>
                                            </TableRow>
                                        ),
                                    )}
                                </TableBody>
                            </Table>
                        </Card>
                    )}

                    <Button variant="outline" asChild>
                        <Link href={route('purchase-orders.index')}>
                            Kembali
                        </Link>
                    </Button>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
