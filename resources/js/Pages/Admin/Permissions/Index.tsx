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
import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';

type Permission = { id: number; name: string };
type PaginatorLink = { url: string | null; label: string; active: boolean };
type Paginator<T> = { data: T[]; links: PaginatorLink[] };

export default function PermissionsIndex({
    permissions,
    filters,
}: {
    permissions: Paginator<Permission>;
    filters: { search: string; per_page: number };
}) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [deleteTarget, setDeleteTarget] = useState<Permission | null>(null);
    const flash = usePage().props.flash;
    const { can } = usePermissions();

    const submit = (e?: React.FormEvent) => {
        e?.preventDefault();
        router.get(
            route('admin.permissions.index'),
            { search, per_page: filters.per_page },
            { preserveState: true, replace: true },
        );
    };

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold">Permissions</h2>}
        >
            <Head title="Permissions" />
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
                        <form onSubmit={submit} className="flex gap-2">
                            <Input
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Cari permission..."
                                className="w-72"
                            />
                            <Button type="submit">Cari</Button>
                        </form>
                        {can('create-permission') && (
                            <Button asChild>
                                <Link href={route('admin.permissions.create')}>
                                    Tambah Permission
                                </Link>
                            </Button>
                        )}
                    </div>
                    <Card>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nama</TableHead>
                                    <TableHead className="text-right">
                                        Aksi
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {permissions.data.length === 0 ? (
                                    <TableRow>
                                        <TableCell
                                            colSpan={2}
                                            className="text-muted-foreground text-center"
                                        >
                                            Tidak ada data
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    permissions.data.map((permission) => (
                                        <TableRow key={permission.id}>
                                            <TableCell>
                                                {permission.name}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <div className="flex justify-end gap-2">
                                                    {can('edit-permission') && (
                                                        <Button
                                                            variant="outline"
                                                            size="sm"
                                                            asChild
                                                        >
                                                            <Link
                                                                href={route(
                                                                    'admin.permissions.edit',
                                                                    permission.id,
                                                                )}
                                                            >
                                                                Edit
                                                            </Link>
                                                        </Button>
                                                    )}
                                                    {can(
                                                        'delete-permission',
                                                    ) && (
                                                        <Button
                                                            variant="destructive"
                                                            size="sm"
                                                            onClick={() =>
                                                                setDeleteTarget(
                                                                    permission,
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
                        {permissions.links.map((link, i) => (
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
                title="Hapus Permission"
                description={
                    deleteTarget
                        ? `Hapus permission ${deleteTarget.name}?`
                        : undefined
                }
                confirmLabel="Hapus"
                destructive
                onClose={() => setDeleteTarget(null)}
                onConfirm={() => {
                    if (!deleteTarget) return;
                    router.delete(
                        route('admin.permissions.destroy', deleteTarget.id),
                        { onFinish: () => setDeleteTarget(null) },
                    );
                }}
            />
        </AuthenticatedLayout>
    );
}
