import InputHint from '@/Components/InputHint';
import { Button } from '@/Components/ui/button';
import { Checkbox } from '@/Components/ui/checkbox';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';

type Permission = { id: number; name: string };

export default function RolesCreate({
    permissions,
}: {
    permissions: Permission[];
}) {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        permissions: [] as number[],
    });

    const toggle = (id: number) => {
        setData(
            'permissions',
            data.permissions.includes(id)
                ? data.permissions.filter((p) => p !== id)
                : [...data.permissions, id],
        );
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('admin.roles.store'));
    };

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold">Tambah Role</h2>}
        >
            <Head title="Tambah Role" />
            <div className="py-6">
                <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                    <form
                        onSubmit={submit}
                        className="bg-card space-y-4 rounded-md border p-6"
                    >
                        <div>
                            <Label>Nama Role</Label>
                            <Input
                                placeholder="admin"
                                value={data.name}
                                onChange={(e) =>
                                    setData('name', e.target.value)
                                }
                            />
                            <InputHint>
                                Gunakan huruf kecil tanpa spasi, contoh: admin,
                                petugas.
                            </InputHint>
                            {errors.name && (
                                <p className="text-destructive text-sm">
                                    {errors.name}
                                </p>
                            )}
                        </div>
                        <div>
                            <Label>Permissions</Label>
                            <InputHint>
                                Centang aksi yang boleh dilakukan role ini.
                            </InputHint>
                            <div className="mt-2 grid gap-2 sm:grid-cols-2">
                                {permissions.map((permission) => (
                                    <label
                                        key={permission.id}
                                        className="flex items-center gap-2 text-sm"
                                    >
                                        <Checkbox
                                            checked={data.permissions.includes(
                                                permission.id,
                                            )}
                                            onCheckedChange={() =>
                                                toggle(permission.id)
                                            }
                                        />
                                        {permission.name}
                                    </label>
                                ))}
                            </div>
                        </div>
                        <div className="flex justify-end gap-2">
                            <Button variant="outline" asChild>
                                <Link href={route('admin.roles.index')}>
                                    Batal
                                </Link>
                            </Button>
                            <Button type="submit" disabled={processing}>
                                Simpan
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
