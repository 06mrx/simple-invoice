import ConfirmDialog from '@/Components/ConfirmDialog';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
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
import { useState } from 'react';

type Bayi = {
    id: string;
    nama: string;
    nik: string;
    tgl_lahir: string;
    jk: 'L' | 'P';
    nama_ortu: string;
    bb?: number | null;
    tb?: number | null;
    ll?: number | null;
    lk?: number | null;
    ket?: string | null;
    deleted_at?: string | null;
};

type PaginatorLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type Paginator<T> = {
    data: T[];
    links: PaginatorLink[];
    meta?: {
        current_page: number;
        last_page: number;
        from: number | null;
        to: number | null;
        total: number;
    };
};

type Props = {
    bayis: Paginator<Bayi>;
    filters: {
        search: string;
        per_page: number;
    };
};

export default function BayiIndex({ bayis, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [deleteTarget, setDeleteTarget] = useState<Bayi | null>(null);
    const { can } = usePermissions();

    const submit = (e?: React.FormEvent) => {
        e?.preventDefault();
        router.get(
            route('bayi.index'),
            { search, per_page: filters.per_page },
            { preserveState: true, replace: true },
        );
    };

    const changePerPage = (value: string) => {
        router.get(
            route('bayi.index'),
            { search: filters.search, per_page: value },
            { preserveState: true, replace: true },
        );
    };

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold">Data Bayi</h2>}
        >
            <Head title="Data Bayi" />

            <div className="py-6">
                <div className="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
                    <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <form onSubmit={submit} className="flex gap-2">
                            <Input
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Cari nama, NIK, orang tua..."
                                className="w-72"
                            />
                            <Button type="submit">Cari</Button>
                        </form>

                        <div className="flex items-center gap-2">
                            <span className="text-muted-foreground text-sm">
                                Per halaman
                            </span>
                            <Select
                                value={String(filters.per_page)}
                                onValueChange={changePerPage}
                            >
                                <SelectTrigger className="w-24">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    {[10, 20, 50, 100].map((n) => (
                                        <SelectItem key={n} value={String(n)}>
                                            {n}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {can('create-bayi') && (
                                <Button asChild>
                                    <Link href={route('bayi.create')}>
                                        Tambah Bayi
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
                                    <TableHead>NIK</TableHead>
                                    <TableHead>Tgl Lahir</TableHead>
                                    <TableHead>JK</TableHead>
                                    <TableHead>Orang Tua</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead className="text-right">
                                        Aksi
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {bayis.data.length === 0 ? (
                                    <TableRow>
                                        <TableCell
                                            colSpan={7}
                                            className="text-muted-foreground text-center"
                                        >
                                            Tidak ada data
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    bayis.data.map((bayi) => (
                                        <TableRow key={bayi.id}>
                                            <TableCell>{bayi.nama}</TableCell>
                                            <TableCell>{bayi.nik}</TableCell>
                                            <TableCell>
                                                {bayi.tgl_lahir}
                                            </TableCell>
                                            <TableCell>{bayi.jk}</TableCell>
                                            <TableCell>
                                                {bayi.nama_ortu}
                                            </TableCell>
                                            <TableCell>
                                                {bayi.deleted_at ? (
                                                    <Badge variant="secondary">
                                                        Terhapus
                                                    </Badge>
                                                ) : (
                                                    <Badge>Aktif</Badge>
                                                )}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <div className="flex justify-end gap-2">
                                                    {bayi.deleted_at ? (
                                                        can('delete-bayi') && (
                                                            <Button
                                                                variant="outline"
                                                                size="sm"
                                                                onClick={() =>
                                                                    router.post(
                                                                        route(
                                                                            'bayi.restore',
                                                                            bayi.id,
                                                                        ),
                                                                    )
                                                                }
                                                            >
                                                                Pulihkan
                                                            </Button>
                                                        )
                                                    ) : (
                                                        <>
                                                            {can(
                                                                'edit-bayi',
                                                            ) && (
                                                                <Button
                                                                    variant="outline"
                                                                    size="sm"
                                                                    asChild
                                                                >
                                                                    <Link
                                                                        href={route(
                                                                            'bayi.edit',
                                                                            bayi.id,
                                                                        )}
                                                                    >
                                                                        Edit
                                                                    </Link>
                                                                </Button>
                                                            )}
                                                            {can(
                                                                'delete-bayi',
                                                            ) && (
                                                                <Button
                                                                    variant="destructive"
                                                                    size="sm"
                                                                    onClick={() =>
                                                                        setDeleteTarget(
                                                                            bayi,
                                                                        )
                                                                    }
                                                                >
                                                                    Hapus
                                                                </Button>
                                                            )}
                                                        </>
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
                        {bayis.links.map((link, i) => (
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
                title="Hapus Data Bayi"
                description={
                    deleteTarget
                        ? `Hapus data bayi ${deleteTarget.nama}?`
                        : undefined
                }
                confirmLabel="Hapus"
                destructive
                onClose={() => setDeleteTarget(null)}
                onConfirm={() => {
                    if (!deleteTarget) return;
                    router.delete(route('bayi.destroy', deleteTarget.id), {
                        onFinish: () => setDeleteTarget(null),
                    });
                }}
            />
        </AuthenticatedLayout>
    );
}
