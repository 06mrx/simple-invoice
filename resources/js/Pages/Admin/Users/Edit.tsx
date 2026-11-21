import InputHint from '@/Components/InputHint';
import { Button } from '@/Components/ui/button';
import { Checkbox } from '@/Components/ui/checkbox';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';

type Role = { id: number; name: string };
type User = { id: number; name: string; email: string; roles?: Role[] };

export default function UsersEdit({
    user,
    roles,
}: {
    user: User;
    roles: Role[];
}) {
    const { data, setData, put, processing, errors } = useForm({
        name: user.name,
        email: user.email,
        password: '',
        roles: user.roles?.map((r) => r.id) ?? [],
    });

    const toggleRole = (id: number) => {
        setData(
            'roles',
            data.roles.includes(id)
                ? data.roles.filter((r) => r !== id)
                : [...data.roles, id],
        );
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('admin.users.update', user.id));
    };

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold">Edit User</h2>}
        >
            <Head title="Edit User" />
            <div className="py-6">
                <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                    <form
                        onSubmit={submit}
                        className="bg-card space-y-4 rounded-md border p-6"
                    >
                        <div>
                            <Label>Nama</Label>
                            <Input
                                placeholder="Nama lengkap"
                                value={data.name}
                                onChange={(e) =>
                                    setData('name', e.target.value)
                                }
                            />
                            {errors.name && (
                                <p className="text-destructive text-sm">
                                    {errors.name}
                                </p>
                            )}
                        </div>
                        <div>
                            <Label>Email</Label>
                            <Input
                                type="email"
                                placeholder="nama@perusahaan.com"
                                value={data.email}
                                onChange={(e) =>
                                    setData('email', e.target.value)
                                }
                            />
                            {errors.email && (
                                <p className="text-destructive text-sm">
                                    {errors.email}
                                </p>
                            )}
                        </div>
                        <div>
                            <Label>
                                Password (kosongkan jika tidak diubah)
                            </Label>
                            <Input
                                type="password"
                                placeholder="Kosongkan jika tidak diubah"
                                value={data.password}
                                onChange={(e) =>
                                    setData('password', e.target.value)
                                }
                            />
                            <InputHint>
                                Isi minimal 6 karakter jika ingin mengganti
                                password.
                            </InputHint>
                            {errors.password && (
                                <p className="text-destructive text-sm">
                                    {errors.password}
                                </p>
                            )}
                        </div>
                        <div>
                            <Label>Roles</Label>
                            <InputHint>
                                Centang role untuk menentukan akses pengguna.
                            </InputHint>
                            <div className="mt-2 grid gap-2 sm:grid-cols-2">
                                {roles.map((role) => (
                                    <label
                                        key={role.id}
                                        className="flex items-center gap-2 text-sm"
                                    >
                                        <Checkbox
                                            checked={data.roles.includes(
                                                role.id,
                                            )}
                                            onCheckedChange={() =>
                                                toggleRole(role.id)
                                            }
                                        />
                                        {role.name}
                                    </label>
                                ))}
                            </div>
                        </div>
                        <div className="flex justify-end gap-2">
                            <Button variant="outline" asChild>
                                <Link href={route('admin.users.index')}>
                                    Batal
                                </Link>
                            </Button>
                            <Button type="submit" disabled={processing}>
                                Perbarui
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
