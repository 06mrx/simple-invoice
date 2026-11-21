import InputHint from '@/Components/InputHint';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function BayiCreate() {
    const { data, setData, post, processing, errors } = useForm({
        nama: '',
        nik: '',
        tgl_lahir: '',
        jk: 'L',
        nama_ortu: '',
        bb: '',
        tb: '',
        ll: '',
        lk: '',
        ket: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('bayi.store'));
    };

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold">Tambah Bayi</h2>}
        >
            <Head title="Tambah Bayi" />
            <div className="py-6">
                <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                    <form
                        onSubmit={submit}
                        className="bg-card space-y-4 rounded-md border p-6"
                    >
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div>
                                <Label>Nama</Label>
                                <Input
                                    placeholder="Nama lengkap bayi"
                                    value={data.nama}
                                    onChange={(e) =>
                                        setData('nama', e.target.value)
                                    }
                                />
                                {errors.nama && (
                                    <p className="text-destructive text-sm">
                                        {errors.nama}
                                    </p>
                                )}
                            </div>
                            <div>
                                <Label>NIK</Label>
                                <Input
                                    placeholder="16 digit NIK"
                                    value={data.nik}
                                    onChange={(e) =>
                                        setData('nik', e.target.value)
                                    }
                                />
                                <InputHint>
                                    Isi sesuai Kartu Keluarga (16 digit).
                                </InputHint>
                                {errors.nik && (
                                    <p className="text-destructive text-sm">
                                        {errors.nik}
                                    </p>
                                )}
                            </div>
                            <div>
                                <Label>Tanggal Lahir</Label>
                                <Input
                                    type="date"
                                    value={data.tgl_lahir}
                                    onChange={(e) =>
                                        setData('tgl_lahir', e.target.value)
                                    }
                                />
                                <InputHint>
                                    Pilih tanggal lahir sesuai akta/KK.
                                </InputHint>
                                {errors.tgl_lahir && (
                                    <p className="text-destructive text-sm">
                                        {errors.tgl_lahir}
                                    </p>
                                )}
                            </div>
                            <div>
                                <Label>Jenis Kelamin</Label>
                                <select
                                    className="border-input bg-background flex h-10 w-full rounded-md border px-3 py-2 text-sm"
                                    value={data.jk}
                                    onChange={(e) =>
                                        setData(
                                            'jk',
                                            e.target.value as 'L' | 'P',
                                        )
                                    }
                                >
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div className="sm:col-span-2">
                                <Label>Nama Orang Tua</Label>
                                <Input
                                    placeholder="Nama orang tua atau wali"
                                    value={data.nama_ortu}
                                    onChange={(e) =>
                                        setData('nama_ortu', e.target.value)
                                    }
                                />
                                {errors.nama_ortu && (
                                    <p className="text-destructive text-sm">
                                        {errors.nama_ortu}
                                    </p>
                                )}
                            </div>
                            <div>
                                <Label>Berat Badan</Label>
                                <Input
                                    type="number"
                                    step="0.01"
                                    placeholder="0.00"
                                    value={data.bb}
                                    onChange={(e) =>
                                        setData('bb', e.target.value)
                                    }
                                />
                                <InputHint>Satuan kilogram (kg).</InputHint>
                            </div>
                            <div>
                                <Label>Tinggi Badan</Label>
                                <Input
                                    type="number"
                                    step="0.01"
                                    placeholder="0.00"
                                    value={data.tb}
                                    onChange={(e) =>
                                        setData('tb', e.target.value)
                                    }
                                />
                                <InputHint>Satuan sentimeter (cm).</InputHint>
                            </div>
                            <div>
                                <Label>Lingkar Lengan</Label>
                                <Input
                                    type="number"
                                    step="0.01"
                                    placeholder="0.00"
                                    value={data.ll}
                                    onChange={(e) =>
                                        setData('ll', e.target.value)
                                    }
                                />
                                <InputHint>Satuan sentimeter (cm).</InputHint>
                            </div>
                            <div>
                                <Label>Lingkar Kepala</Label>
                                <Input
                                    type="number"
                                    step="0.01"
                                    placeholder="0.00"
                                    value={data.lk}
                                    onChange={(e) =>
                                        setData('lk', e.target.value)
                                    }
                                />
                                <InputHint>Satuan sentimeter (cm).</InputHint>
                            </div>
                            <div className="sm:col-span-2">
                                <Label>Keterangan</Label>
                                <Textarea
                                    placeholder="Contoh: kondisi khusus, alergi, atau catatan pertumbuhan"
                                    value={data.ket}
                                    onChange={(e) =>
                                        setData('ket', e.target.value)
                                    }
                                />
                                <InputHint>
                                    Opsional. Isi jika ada catatan penting.
                                </InputHint>
                            </div>
                        </div>

                        <div className="flex justify-end gap-2">
                            <Button variant="outline" asChild>
                                <Link href={route('bayi.index')}>Batal</Link>
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
