import { Avatar, AvatarFallback } from '@/Components/ui/avatar';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card';
import { Separator } from '@/Components/ui/separator';
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
import { Head, Link, usePage } from '@inertiajs/react';
import {
    AlertTriangle,
    ArrowDownRight,
    ArrowUpRight,
    CalendarDays,
    ChevronRight,
    ClipboardList,
    Download,
    FileText,
    Plus,
    Send,
    Settings,
    TrendingUp,
    Users,
} from 'lucide-react';

type Trend = { current: number; previous: number; percent: number };

type Metrics = {
    total_invoices: number;
    total_revenue: number;
    sent_count: number;
    sent_revenue: number;
    outstanding: number;
    failed_count: number;
    delivery_rate: number;
    total_customers: number;
    total_purchase_orders: number;
    installation_purchase_orders: number;
};

type StatusCounts = { generated: number; sent: number; failed: number };
type MonthlyPoint = { label: string; total: number; count: number };

type RecentInvoice = {
    id: string;
    invoice_number: string;
    invoice_date: string;
    total: number;
    status: string;
    customer: { code: string; name: string };
    po_code: string;
};

type RecentCustomer = {
    id: string;
    code: string;
    name: string;
    email: string;
    created_at: string;
};

type TopCustomer = {
    id: string;
    code: string;
    name: string;
    invoice_count: number;
    revenue: number;
};

type Props = {
    period: string;
    metrics: Metrics;
    trends: { invoices: Trend; revenue: Trend; sent: Trend };
    status_counts: StatusCounts;
    monthly: MonthlyPoint[];
    recent_invoices: RecentInvoice[];
    recent_customers: RecentCustomer[];
    top_customers: TopCustomer[];
    purchase_orders: { total: number; this_month: number };
};

const rupiah = (value: number) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value);

const shortRupiah = (value: number) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        notation: 'compact',
        maximumFractionDigits: 1,
    }).format(value);

/* date formatted via shared util */

const initials = (name: string) =>
    name
        .split(' ')
        .slice(0, 2)
        .map((word) => word.charAt(0))
        .join('')
        .toUpperCase();

const statusVariant = (status: string) => {
    if (status === 'sent') return 'default' as const;
    if (status === 'failed') return 'destructive' as const;
    return 'secondary' as const;
};

function TrendBadge({ trend }: { trend: Trend }) {
    const positive = trend.percent >= 0;
    const Icon = positive ? ArrowUpRight : ArrowDownRight;

    return (
        <span
            className={`flex items-center gap-0.5 text-xs font-medium ${
                positive ? 'text-emerald-600' : 'text-red-600'
            }`}
        >
            <Icon className="h-3.5 w-3.5" />
            {Math.abs(trend.percent)}%
            <span className="text-muted-foreground font-normal">
                vs bulan lalu
            </span>
        </span>
    );
}

export default function InvoiceDashboard({
    period,
    metrics,
    trends,
    status_counts,
    monthly,
    recent_invoices,
    recent_customers,
    top_customers,
    purchase_orders,
}: Props) {
    const { can } = usePermissions();
    const user = usePage().props.auth.user as { name: string };
    const chartMax = Math.max(...monthly.map((item) => item.total), 1);
    const statusTotal = Math.max(
        status_counts.generated + status_counts.sent + status_counts.failed,
        1,
    );
    const sentPercent = (status_counts.sent / statusTotal) * 100;
    const generatedPercent = (status_counts.generated / statusTotal) * 100;
    const topCustomerMax = Math.max(
        ...top_customers.map((customer) => customer.revenue),
        1,
    );

    const cards = [
        {
            label: 'Total Invoice',
            value: String(metrics.total_invoices),
            icon: FileText,
            trend: trends.invoices,
            accent: 'bg-blue-500/10 text-blue-600',
        },
        {
            label: 'Total Tagihan',
            value: rupiah(metrics.total_revenue),
            icon: TrendingUp,
            trend: trends.revenue,
            accent: 'bg-emerald-500/10 text-emerald-600',
        },
        {
            label: 'Sudah Dikirim',
            value: String(metrics.sent_count),
            icon: Send,
            trend: trends.sent,
            accent: 'bg-violet-500/10 text-violet-600',
        },
        {
            label: 'Perlu Ditindaklanjuti',
            value: String(status_counts.generated + status_counts.failed),
            icon: AlertTriangle,
            trend: null,
            accent: 'bg-amber-500/10 text-amber-600',
            note: `${metrics.failed_count} gagal dikirim`,
        },
    ];

    return (
        <AuthenticatedLayout
            header={
                <div className="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 className="text-xl font-semibold">
                            Dashboard Invoice
                        </h2>
                        <p className="text-muted-foreground text-sm">
                            Ringkasan operasional invoice Anda
                        </p>
                    </div>
                    <div className="flex items-center gap-2">
                        <span className="border-input bg-background flex items-center gap-2 rounded-md border px-3 py-2 text-sm">
                            <CalendarDays className="text-muted-foreground h-4 w-4" />
                            {period}
                        </span>
                        {can('view-invoice') && (
                            <Button variant="outline" size="sm" asChild>
                                <Link href={route('invoices.index')}>
                                    <Download className="h-4 w-4" />
                                    Lihat Invoice
                                </Link>
                            </Button>
                        )}
                    </div>
                </div>
            }
        >
            <Head title="Dashboard Invoice" />
            <div className="mx-auto max-w-7xl space-y-6 py-6">
                <div className="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">
                            Selamat datang, {user.name}
                        </h1>
                        <p className="text-muted-foreground mt-1 text-sm">
                            Pantau tagihan, customer, dan purchase order dalam
                            satu tempat.
                        </p>
                    </div>
                    <Badge variant="secondary">Workspace Invoice</Badge>
                </div>

                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    {cards.map((card) => (
                        <Card key={card.label}>
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardDescription>{card.label}</CardDescription>
                                <span
                                    className={`flex h-8 w-8 items-center justify-center rounded-md ${card.accent}`}
                                >
                                    <card.icon className="h-4 w-4" />
                                </span>
                            </CardHeader>
                            <CardContent className="space-y-1">
                                <div className="text-2xl font-semibold tracking-tight">
                                    {card.value}
                                </div>
                                {card.trend ? (
                                    <TrendBadge trend={card.trend} />
                                ) : (
                                    <p className="text-muted-foreground text-xs">
                                        {card.note}
                                    </p>
                                )}
                            </CardContent>
                        </Card>
                    ))}
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    <Card className="lg:col-span-2">
                        <CardHeader className="flex flex-row items-start justify-between space-y-0">
                            <div>
                                <CardTitle>Tren Pendapatan</CardTitle>
                                <CardDescription>
                                    Total nilai invoice 6 bulan terakhir
                                </CardDescription>
                            </div>
                            <Badge variant="outline">6 bulan</Badge>
                        </CardHeader>
                        <CardContent>
                            <div className="flex h-56 items-end gap-2 sm:gap-4">
                                {monthly.map((item) => (
                                    <div
                                        key={item.label}
                                        className="group flex h-full flex-1 flex-col items-center justify-end gap-2"
                                        title={`${item.label}: ${rupiah(item.total)} (${item.count} invoice)`}
                                    >
                                        <span className="text-muted-foreground text-[10px] opacity-0 transition-opacity group-hover:opacity-100">
                                            {shortRupiah(item.total)}
                                        </span>
                                        <div className="flex h-full w-full items-end justify-center">
                                            <div
                                                className="bg-primary/85 group-hover:bg-primary w-full max-w-12 rounded-t-md transition-all"
                                                style={{
                                                    height: `${Math.max(4, (item.total / chartMax) * 100)}%`,
                                                }}
                                            />
                                        </div>
                                        <span className="text-muted-foreground text-xs">
                                            {item.label}
                                        </span>
                                    </div>
                                ))}
                            </div>
                            <Separator className="my-4" />
                            <div className="grid gap-3 sm:grid-cols-3">
                                <div>
                                    <p className="text-muted-foreground text-xs">
                                        Tagihan bulan ini
                                    </p>
                                    <p className="font-semibold">
                                        {rupiah(trends.revenue.current)}
                                    </p>
                                </div>
                                <div>
                                    <p className="text-muted-foreground text-xs">
                                        Belum dibayar / dikirim
                                    </p>
                                    <p className="font-semibold">
                                        {rupiah(metrics.outstanding)}
                                    </p>
                                </div>
                                <div>
                                    <p className="text-muted-foreground text-xs">
                                        Sudah dikirim
                                    </p>
                                    <p className="font-semibold">
                                        {rupiah(metrics.sent_revenue)}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Status Invoice</CardTitle>
                            <CardDescription>
                                Distribusi seluruh invoice Anda
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-5">
                            <div className="flex justify-center">
                                <div
                                    className="relative h-40 w-40 rounded-full"
                                    style={{
                                        background: `conic-gradient(#10b981 0% ${sentPercent}%, #f59e0b ${sentPercent}% ${sentPercent + generatedPercent}%, #ef4444 ${sentPercent + generatedPercent}% 100%)`,
                                    }}
                                >
                                    <div className="bg-card absolute inset-5 flex flex-col items-center justify-center rounded-full">
                                        <span className="text-2xl font-semibold">
                                            {metrics.delivery_rate}%
                                        </span>
                                        <span className="text-muted-foreground text-xs">
                                            terkirim
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div className="space-y-2 text-sm">
                                <div className="flex items-center justify-between">
                                    <span className="flex items-center gap-2">
                                        <span className="h-2 w-2 rounded-full bg-emerald-500" />
                                        Terkirim
                                    </span>
                                    <span className="font-medium">
                                        {status_counts.sent}
                                    </span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="flex items-center gap-2">
                                        <span className="h-2 w-2 rounded-full bg-amber-500" />
                                        Generated
                                    </span>
                                    <span className="font-medium">
                                        {status_counts.generated}
                                    </span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="flex items-center gap-2">
                                        <span className="h-2 w-2 rounded-full bg-red-500" />
                                        Gagal
                                    </span>
                                    <span className="font-medium">
                                        {status_counts.failed}
                                    </span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    <Card className="lg:col-span-2">
                        <CardHeader className="flex flex-row items-start justify-between space-y-0">
                            <div>
                                <CardTitle>Invoice Terbaru</CardTitle>
                                <CardDescription>
                                    Aktivitas penagihan terakhir
                                </CardDescription>
                            </div>
                            {can('view-invoice') && (
                                <Button variant="ghost" size="sm" asChild>
                                    <Link href={route('invoices.index')}>
                                        Lihat semua
                                        <ChevronRight className="h-4 w-4" />
                                    </Link>
                                </Button>
                            )}
                        </CardHeader>
                        <CardContent>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Invoice</TableHead>
                                        <TableHead>Customer</TableHead>
                                        <TableHead>Tanggal</TableHead>
                                        <TableHead className="text-right">
                                            Jumlah
                                        </TableHead>
                                        <TableHead>Status</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {recent_invoices.length === 0 ? (
                                        <TableRow>
                                            <TableCell
                                                colSpan={5}
                                                className="text-muted-foreground py-8 text-center"
                                            >
                                                Belum ada invoice.
                                            </TableCell>
                                        </TableRow>
                                    ) : (
                                        recent_invoices.map((invoice) => (
                                            <TableRow key={invoice.id}>
                                                <TableCell>
                                                    <div className="font-medium">
                                                        {invoice.invoice_number}
                                                    </div>
                                                    <div className="text-muted-foreground text-xs">
                                                        PO {invoice.po_code}
                                                    </div>
                                                </TableCell>
                                                <TableCell>
                                                    <div className="text-sm">
                                                        {invoice.customer.name}
                                                    </div>
                                                    <div className="text-muted-foreground text-xs">
                                                        {invoice.customer.code}
                                                    </div>
                                                </TableCell>
                                                <TableCell className="text-sm">
                                                    {formatDate(
                                                        invoice.invoice_date,
                                                    )}
                                                </TableCell>
                                                <TableCell className="text-right text-sm font-medium">
                                                    {rupiah(invoice.total)}
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
                                            </TableRow>
                                        ))
                                    )}
                                </TableBody>
                            </Table>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-start justify-between space-y-0">
                            <div>
                                <CardTitle>Customer Terbaru</CardTitle>
                                <CardDescription>
                                    Data yang Anda tambahkan
                                </CardDescription>
                            </div>
                            <Users className="text-muted-foreground h-4 w-4" />
                        </CardHeader>
                        <CardContent className="space-y-4">
                            {recent_customers.length === 0 ? (
                                <p className="text-muted-foreground text-sm">
                                    Belum ada customer.
                                </p>
                            ) : (
                                recent_customers.map((customer) => (
                                    <div
                                        key={customer.id}
                                        className="flex items-center gap-3"
                                    >
                                        <Avatar className="h-9 w-9">
                                            <AvatarFallback>
                                                {initials(customer.name)}
                                            </AvatarFallback>
                                        </Avatar>
                                        <div className="min-w-0 flex-1">
                                            <p className="truncate text-sm font-medium">
                                                {customer.name}
                                            </p>
                                            <p className="text-muted-foreground truncate text-xs">
                                                {customer.code} ·{' '}
                                                {customer.email}
                                            </p>
                                        </div>
                                        <span className="text-muted-foreground text-xs">
                                            {formatDate(customer.created_at)}
                                        </span>
                                    </div>
                                ))
                            )}
                        </CardContent>
                    </Card>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    <Card>
                        <CardHeader>
                            <CardTitle>Ringkasan Operasional</CardTitle>
                            <CardDescription>
                                Data master dan instalasi
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="grid grid-cols-2 gap-4">
                            <div className="rounded-md border p-3">
                                <p className="text-muted-foreground text-xs">
                                    Customer
                                </p>
                                <p className="text-xl font-semibold">
                                    {metrics.total_customers}
                                </p>
                            </div>
                            <div className="rounded-md border p-3">
                                <p className="text-muted-foreground text-xs">
                                    Total PO
                                </p>
                                <p className="text-xl font-semibold">
                                    {purchase_orders.total}
                                </p>
                            </div>
                            <div className="rounded-md border p-3">
                                <p className="text-muted-foreground text-xs">
                                    PO bulan ini
                                </p>
                                <p className="text-xl font-semibold">
                                    {purchase_orders.this_month}
                                </p>
                            </div>
                            <div className="rounded-md border p-3">
                                <p className="text-muted-foreground text-xs">
                                    PO dengan instalasi
                                </p>
                                <p className="text-xl font-semibold">
                                    {metrics.installation_purchase_orders}
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Customer Teratas</CardTitle>
                            <CardDescription>
                                Berdasarkan total tagihan
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            {top_customers.length === 0 ? (
                                <p className="text-muted-foreground text-sm">
                                    Belum ada data.
                                </p>
                            ) : (
                                top_customers.map((customer) => (
                                    <div
                                        key={customer.id}
                                        className="space-y-1"
                                    >
                                        <div className="flex items-center justify-between gap-2 text-sm">
                                            <span className="truncate font-medium">
                                                {customer.name}
                                            </span>
                                            <span className="shrink-0 text-xs">
                                                {shortRupiah(customer.revenue)}
                                            </span>
                                        </div>
                                        <div className="bg-muted h-1.5 w-full overflow-hidden rounded-full">
                                            <div
                                                className="bg-primary h-full rounded-full"
                                                style={{
                                                    width: `${(customer.revenue / topCustomerMax) * 100}%`,
                                                }}
                                            />
                                        </div>
                                        <p className="text-muted-foreground text-xs">
                                            {customer.invoice_count} invoice
                                        </p>
                                    </div>
                                ))
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Aksi Cepat</CardTitle>
                            <CardDescription>
                                Pintasan ke modul invoice
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="grid gap-2">
                            {can('create-invoice') && (
                                <Button variant="outline" asChild>
                                    <Link href={route('invoices.index')}>
                                        <Plus className="h-4 w-4" />
                                        Generate invoice
                                    </Link>
                                </Button>
                            )}
                            {can('view-customer') && (
                                <Button variant="outline" asChild>
                                    <Link href={route('customers.index')}>
                                        <Users className="h-4 w-4" />
                                        Kelola customer
                                    </Link>
                                </Button>
                            )}
                            {can('view-purchaseorder') && (
                                <Button variant="outline" asChild>
                                    <Link href={route('purchase-orders.index')}>
                                        <ClipboardList className="h-4 w-4" />
                                        Kelola purchase order
                                    </Link>
                                </Button>
                            )}
                            {can('view-invoicesetting') && (
                                <Button variant="outline" asChild>
                                    <Link href={route('invoice-settings.edit')}>
                                        <Settings className="h-4 w-4" />
                                        Pengaturan invoice
                                    </Link>
                                </Button>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
