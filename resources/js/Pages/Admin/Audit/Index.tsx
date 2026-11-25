import { Badge } from '@/Components/ui/badge';
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
import { formatDate } from '@/lib/utils';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';

type User = { id: number; name: string };
type Audit = {
    id: string | number;
    event: string;
    auditable_type: string;
    auditable_id: string | number;
    user?: User | null;
    created_at: string;
};
type PaginatorLink = { url: string | null; label: string; active: boolean };
type Paginator<T> = { data: T[]; links: PaginatorLink[] };

export default function AuditIndex({
    audits,
    filters,
}: {
    audits: Paginator<Audit>;
    filters: {
        search: string;
        event: string;
        user_id: string;
        per_page: number;
    };
}) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [event, setEvent] = useState(filters.event ?? '');
    const flash = usePage().props.flash;

    const submit = (e?: React.FormEvent) => {
        e?.preventDefault();
        router.get(
            route('admin.audit.index'),
            {
                search,
                event,
                user_id: filters.user_id,
                per_page: filters.per_page,
            },
            { preserveState: true, replace: true },
        );
    };

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold">Audit Log</h2>}
        >
            <Head title="Audit Log" />
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
                    <form
                        onSubmit={submit}
                        className="flex flex-col gap-2 sm:flex-row"
                    >
                        <Input
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Cari event/model/user..."
                            className="w-72"
                        />
                        <Input
                            value={event}
                            onChange={(e) => setEvent(e.target.value)}
                            placeholder="Event"
                            className="w-40"
                        />
                        <Button type="submit">Filter</Button>
                    </form>

                    <Card>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Waktu</TableHead>
                                    <TableHead>Event</TableHead>
                                    <TableHead>Model</TableHead>
                                    <TableHead>User</TableHead>
                                    <TableHead className="text-right">
                                        Aksi
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {audits.data.length === 0 ? (
                                    <TableRow>
                                        <TableCell
                                            colSpan={5}
                                            className="text-muted-foreground text-center"
                                        >
                                            Tidak ada data
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    audits.data.map((audit) => (
                                        <TableRow key={audit.id}>
                                            <TableCell>
                                                {formatDate(audit.created_at)}
                                            </TableCell>
                                            <TableCell>
                                                <Badge>{audit.event}</Badge>
                                            </TableCell>
                                            <TableCell>
                                                {audit.auditable_type} #
                                                {audit.auditable_id}
                                            </TableCell>
                                            <TableCell>
                                                {audit.user?.name ?? '-'}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    asChild
                                                >
                                                    <Link
                                                        href={route(
                                                            'admin.audit.show',
                                                            audit.id,
                                                        )}
                                                    >
                                                        Detail
                                                    </Link>
                                                </Button>
                                            </TableCell>
                                        </TableRow>
                                    ))
                                )}
                            </TableBody>
                        </Table>
                    </Card>
                    <div className="flex flex-wrap gap-1">
                        {audits.links.map((link, i) => (
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
        </AuthenticatedLayout>
    );
}
