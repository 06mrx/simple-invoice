import InputError from '@/Components/InputError';
import InputHint from '@/Components/InputHint';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function PermissionsCreate() {
    const { data, setData, post, processing, errors } = useForm({ name: '' });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('admin.permissions.store'));
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold">Tambah Permission</h2>
            }
        >
            <Head title="Tambah Permission" />
            <div className="py-6">
                <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                    <form
                        onSubmit={submit}
                        className="bg-card space-y-4 rounded-md border p-6"
                    >
                        <div>
                            <Label>Nama Permission</Label>
                            <Input
                                value={data.name}
                                placeholder="view-bayi"
                                onChange={(e) =>
                                    setData('name', e.target.value)
                                }
                            />
                            <InputHint>
                                Format disarankan: view-bayi, create-user,
                                edit-role.
                            </InputHint>
                            <InputError message={errors.name} />
                        </div>
                        <div className="flex justify-end gap-2">
                            <Button variant="outline" asChild>
                                <Link href={route('admin.permissions.index')}>
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
