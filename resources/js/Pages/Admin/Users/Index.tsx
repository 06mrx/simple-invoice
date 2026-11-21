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

type Role = { id: number; name: string };
type User = {
    id: number;
    name: string;
    email: string;
    roles?: Role[];
    deleted_at?: string | null;
};

type PaginatorLink = { url: string | null; label: string; active: boolean };
type Paginator<T> = { data: T[]; links: PaginatorLink[] };

type Props = {
    users: Paginator<User>;
    filters: { search: string; per_page: number };
    roles: Role[];
};

export default function UsersIndex({ users, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const flash = usePage().props.flash;
    const { can } = usePermissions();

    const submit = (e?: React.FormEvent) => {
        e?.preventDefault();
        router.get(
            route('admin.users.index'),
            { search, per_page: filters.per_page },
            { preserveState: true, replace: true },
        );
    };

    const changePerPage = (value: string) => {
        router.get(
            route('admin.users.index'),
            { search: filters.search, per_page: value },
            { preserveState: true, replace: true },
        );
    };

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold">Users</h2>}
        >
            <Head title="Users" />
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
                                placeholder="Cari nama/email..."
                                className="w-72"
                            />
                            <Button type="submit">Cari</Button>
                        </form>
                        <div className="flex items-center gap-2">
                            <select
                                className="border-input bg-background h-10 rounded-md border px-3 text-sm"
                                value={filters.per_page}
                                onChange={(e) => changePerPage(e.target.value)}
                            >
                                {[10, 20, 50, 100].map((n) => (
                                    <option key={n} value={n}>
                                        {n}
                                    </option>
                                ))}
                            </select>
                            {can('create-user') && (
                                <Button asChild>
                                    <Link href={route('admin.users.create')}>
                                        Tambah User
                                    </Link>
                                </Button>
                            )}
                        </div>
                    </div>

                    <Card>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nama</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>Role</TableHead>
                                    <TableHead className="text-right">
                                        Aksi
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {users.data.length === 0 ? (
                                    <TableRow>
                                        <TableCell
                                            colSpan={4}
                                            className="text-muted-foreground text-center"
                                        >
                                            Tidak ada data
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    users.data.map((user) => (
                                        <TableRow key={user.id}>
                                            <TableCell>{user.name}</TableCell>
                                            <TableCell>{user.email}</TableCell>
                                            <TableCell>
                                                <div className="flex flex-wrap gap-1">
                                                    {user.roles?.map((role) => (
                                                        <Badge
                                                            key={role.id}
                                                            variant="secondary"
                                                        >
                                                            {role.name}
                                                        </Badge>
                                                    ))}
                                                </div>
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <div className="flex justify-end gap-2">
                                                    {can('edit-user') && (
                                                        <Button
                                                            variant="outline"
                                                            size="sm"
                                                            asChild
                                                        >
                                                            <Link
                                                                href={route(
                                                                    'admin.users.edit',
                                                                    user.id,
                                                                )}
                                                            >
                                                                Edit
                                                            </Link>
                                                        </Button>
                                                    )}
                                                    {can('delete-user') && (
                                                        <Button
                                                            variant="destructive"
                                                            size="sm"
                                                            onClick={() => {
                                                                if (
                                                                    confirm(
                                                                        'Hapus user?',
                                                                    )
                                                                )
                                                                    router.delete(
                                                                        route(
                                                                            'admin.users.destroy',
                                                                            user.id,
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
                        {users.links.map((link, i) => (
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
