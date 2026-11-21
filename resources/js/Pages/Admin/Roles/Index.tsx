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
import { usePermissions } from '@/hooks/use-permissions';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';

type Role = {
    id: number;
    name: string;
    permissions?: { id: number; name: string }[];
};
type PaginatorLink = { url: string | null; label: string; active: boolean };
type Paginator<T> = { data: T[]; links: PaginatorLink[] };

export default function RolesIndex({
    roles,
    filters,
}: {
    roles: Paginator<Role>;
    filters: { search: string; per_page: number };
}) {
    const [search, setSearch] = useState(filters.search ?? '');
    const flash = usePage().props.flash;
    const { can } = usePermissions();

    const submit = (e?: React.FormEvent) => {
        e?.preventDefault();
        router.get(
            route('admin.roles.index'),
            { search, per_page: filters.per_page },
            { preserveState: true, replace: true },
        );
    };

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold">Roles</h2>}
        >
            <Head title="Roles" />
            <div className="py-6">
                <div className="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
                    {flash?.success && (
                        <div className="rounded-md border border-green-200 bg-green-50 p-3 text-sm text-green-700">
                            {flash.success}
                        </div>
                    )}
                    <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <form onSubmit={submit} className="flex gap-2">
                            <Input
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Cari role..."
                                className="w-72"
                            />
                            <Button type="submit">Cari</Button>
                        </form>
                        {can('create-role') && (
                            <Button asChild>
                                <Link href={route('admin.roles.create')}>
                                    Tambah Role
                                </Link>
                            </Button>
                        )}
                    </div>
                    <Card>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nama</TableHead>
                                    <TableHead>Permissions</TableHead>
                                    <TableHead className="text-right">
                                        Aksi
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {roles.data.length === 0 ? (
                                    <TableRow>
                                        <TableCell
                                            colSpan={3}
                                            className="text-muted-foreground text-center"
                                        >
                                            Tidak ada data
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    roles.data.map((role) => (
                                        <TableRow key={role.id}>
                                            <TableCell>{role.name}</TableCell>
                                            <TableCell>
                                                <div className="flex flex-wrap gap-1">
                                                    {role.permissions
                                                        ?.length ? (
                                                        role.permissions.map(
                                                            (p) => (
                                                                <Badge
                                                                    key={p.id}
                                                                    variant="secondary"
                                                                >
                                                                    {p.name}
                                                                </Badge>
                                                            ),
                                                        )
                                                    ) : (
                                                        <span className="text-muted-foreground text-sm">
                                                            —
                                                        </span>
                                                    )}
                                                </div>
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <div className="flex justify-end gap-2">
                                                    {can('edit-role') && (
                                                        <Button
                                                            variant="outline"
                                                            size="sm"
                                                            asChild
                                                        >
                                                            <Link
                                                                href={route(
                                                                    'admin.roles.edit',
                                                                    role.id,
                                                                )}
                                                            >
                                                                Edit
                                                            </Link>
                                                        </Button>
                                                    )}
                                                    {can('delete-role') && (
                                                        <Button
                                                            variant="destructive"
                                                            size="sm"
                                                            onClick={() => {
                                                                if (
                                                                    confirm(
                                                                        'Hapus role?',
                                                                    )
                                                                )
                                                                    router.delete(
                                                                        route(
                                                                            'admin.roles.destroy',
                                                                            role.id,
                                                                        ),
                                                                    );
                                                            }}
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
                        {roles.links.map((link, i) => (
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
