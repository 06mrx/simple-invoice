import ApplicationLogo from '@/Components/ApplicationLogo';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Head, Link } from '@inertiajs/react';
import {
    ArrowRight,
    ChartNoAxesColumn,
    CircleCheck,
    ClipboardList,
    FileSpreadsheet,
    FileText,
    Mail,
    ReceiptText,
    Send,
    ShieldCheck,
    Sparkles,
    Users,
    Workflow,
    Zap,
    type LucideIcon,
} from 'lucide-react';

type WelcomeProps = {
    auth: {
        user: {
            name: string;
        } | null;
    };
    canLogin: boolean;
    canRegister: boolean;
};

type Feature = {
    icon: LucideIcon;
    title: string;
    description: string;
};

const features: Feature[] = [
    {
        icon: Users,
        title: 'Customer & purchase order',
        description:
            'Kelola identitas customer, NPWP, alamat, item PO, dan biaya instalasi dalam satu alur data.',
    },
    {
        icon: Zap,
        title: 'Invoice otomatis',
        description:
            'Nomor, periode, PPN, serta tanggal tagihan mengikuti pengaturan invoice dan aturan generate harian.',
    },
    {
        icon: FileSpreadsheet,
        title: 'PDF & Excel siap kirim',
        description:
            'Buat arsip PDF dan spreadsheet dari invoice yang sama tanpa menghitung ulang data tagihan.',
    },
    {
        icon: Mail,
        title: 'Email pelanggan',
        description:
            'Kirim invoice langsung ke customer dan pantau status terkirim atau gagal dengan jelas.',
    },
    {
        icon: ChartNoAxesColumn,
        title: 'Dashboard operasional',
        description:
            'Pantau tren pendapatan, status invoice, customer terbaru, serta PO aktif dari dashboard operasional.',
    },
    {
        icon: ShieldCheck,
        title: 'Akses & ownership aman',
        description:
            'Role, permission, audit trail, dan pembatasan data per user menjaga setiap transaksi tetap terlacak.',
    },
];

const steps = [
    {
        icon: Users,
        title: 'Tambahkan customer',
        description:
            'Simpan profil, kontak, NPWP, dan informasi billing customer.',
    },
    {
        icon: ClipboardList,
        title: 'Catat purchase order',
        description:
            'Masukkan item layanan, harga, serta biaya instalasi bila diperlukan.',
    },
    {
        icon: ReceiptText,
        title: 'Generate invoice',
        description:
            'Buat nomor dan periode tagihan otomatis dengan PPN yang benar.',
    },
    {
        icon: Send,
        title: 'Kirim & pantau',
        description:
            'Unduh PDF atau Excel, kirim email, lalu pantau status penagihan.',
    },
];

export default function Welcome({ auth, canLogin, canRegister }: WelcomeProps) {
    return (
        <div className="min-h-screen bg-white text-slate-950">
            <Head title="Invoice Management" />
            <a
                href="#konten-utama"
                className="fixed top-4 left-4 z-50 -translate-y-24 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-lg transition-transform focus:translate-y-0 focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 focus:outline-none"
            >
                Lewati ke konten utama
            </a>

            <header className="sticky top-0 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl">
                <div className="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-6 py-4">
                    <Link
                        href="/"
                        aria-current="page"
                        className="flex items-center gap-2.5 rounded-lg focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 focus-visible:outline-none"
                    >
                        <ApplicationLogo className="h-9 w-9 text-blue-600" />
                        <span className="text-xl font-bold tracking-tight">
                            xInvoice
                        </span>
                    </Link>

                    <nav
                        aria-label="Navigasi utama"
                        className="order-3 flex w-full flex-wrap items-center gap-1 sm:order-none sm:w-auto"
                    >
                        <a
                            href="#fitur"
                            className="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-950 focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:outline-none"
                        >
                            Fitur
                        </a>
                        <a
                            href="#alur-kerja"
                            className="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-950 focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:outline-none"
                        >
                            Alur kerja
                        </a>
                        <a
                            href="#keamanan"
                            className="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-950 focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:outline-none"
                        >
                            Keamanan
                        </a>
                    </nav>

                    <div className="flex items-center gap-2">
                        {auth.user ? (
                            <Button asChild size="lg" className="h-10 px-4">
                                <Link href={route('dashboard')}>
                                    Buka dashboard
                                    <ArrowRight aria-hidden="true" />
                                </Link>
                            </Button>
                        ) : (
                            <>
                                {canRegister && (
                                    <Link
                                        href={route('register')}
                                        className="hidden rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-100 hover:text-slate-950 focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:outline-none sm:inline-flex sm:min-h-10 sm:items-center"
                                    >
                                        Daftar
                                    </Link>
                                )}
                                {canLogin && (
                                    <Button
                                        asChild
                                        size="lg"
                                        className="h-10 px-4"
                                    >
                                        <Link href={route('login')}>
                                            Masuk
                                            <ArrowRight aria-hidden="true" />
                                        </Link>
                                    </Button>
                                )}
                            </>
                        )}
                    </div>
                </div>
            </header>

            <main id="konten-utama" tabIndex={-1}>
                <section className="relative overflow-clip bg-slate-950 text-white">
                    <div
                        aria-hidden="true"
                        className="absolute -top-40 right-[-12rem] h-[28rem] w-[28rem] rounded-full bg-blue-600/20 blur-3xl"
                    />
                    <div
                        aria-hidden="true"
                        className="absolute bottom-[-16rem] left-[-10rem] h-96 w-96 rounded-full bg-violet-600/20 blur-3xl"
                    />

                    <div className="relative mx-auto grid max-w-7xl gap-14 px-6 py-20 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:py-28">
                        <div>
                            <Badge className="border-blue-400/30 bg-blue-400/10 px-3 py-1 text-blue-100 hover:bg-blue-400/10">
                                <Sparkles aria-hidden="true" />
                                Invoice workspace untuk tim modern
                            </Badge>

                            <h1 className="mt-6 max-w-3xl text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
                                Kelola invoice, customer, dan PO tanpa kerja
                                berulang.
                            </h1>

                            <p className="mt-6 max-w-2xl text-lg leading-8 text-slate-300">
                                xInvoice menyatukan data master, penagihan,
                                dokumen PDF dan Excel, email pelanggan, serta
                                dashboard operasional dalam satu aplikasi.
                            </p>

                            <div className="mt-8 flex flex-wrap gap-3">
                                <Button
                                    asChild
                                    size="lg"
                                    className="h-11 bg-blue-600 px-5 text-white hover:bg-blue-500"
                                >
                                    {auth.user ? (
                                        <Link href={route('dashboard')}>
                                            Lihat dashboard
                                            <ArrowRight aria-hidden="true" />
                                        </Link>
                                    ) : (
                                        <Link href={route('login')}>
                                            Mulai kelola invoice
                                            <ArrowRight aria-hidden="true" />
                                        </Link>
                                    )}
                                </Button>
                                <Button
                                    asChild
                                    variant="outline"
                                    size="lg"
                                    className="h-11 border-white/25 bg-transparent px-5 text-white hover:border-white/40 hover:bg-white/10 hover:text-white"
                                >
                                    <a href="#fitur">
                                        Jelajahi fitur
                                        <ArrowRight aria-hidden="true" />
                                    </a>
                                </Button>
                            </div>

                            <ul className="mt-8 flex flex-wrap gap-x-6 gap-y-3 text-sm text-slate-300">
                                <li className="flex items-center gap-2">
                                    <CircleCheck
                                        aria-hidden="true"
                                        className="h-4 w-4 text-blue-400"
                                    />
                                    Data scoped per user
                                </li>
                                <li className="flex items-center gap-2">
                                    <CircleCheck
                                        aria-hidden="true"
                                        className="h-4 w-4 text-blue-400"
                                    />
                                    Dokumen PDF & Excel
                                </li>
                                <li className="flex items-center gap-2">
                                    <CircleCheck
                                        aria-hidden="true"
                                        className="h-4 w-4 text-blue-400"
                                    />
                                    Audit trail transparan
                                </li>
                            </ul>
                        </div>

                        <div
                            aria-hidden="true"
                            className="relative mx-auto w-full max-w-xl lg:max-w-none"
                        >
                            <div className="rounded-3xl border border-white/10 bg-white/10 p-3 shadow-2xl shadow-blue-950/40 backdrop-blur">
                                <div className="overflow-hidden rounded-2xl bg-slate-900">
                                    <div className="flex items-center justify-between border-b border-white/10 px-5 py-4">
                                        <div className="flex items-center gap-2">
                                            <span className="h-2.5 w-2.5 rounded-full bg-red-400" />
                                            <span className="h-2.5 w-2.5 rounded-full bg-amber-400" />
                                            <span className="h-2.5 w-2.5 rounded-full bg-emerald-400" />
                                        </div>
                                        <div className="h-2.5 w-24 rounded-full bg-white/15" />
                                    </div>

                                    <div className="space-y-5 p-5 sm:p-6">
                                        <div className="grid grid-cols-2 gap-3">
                                            <div className="rounded-xl border border-white/10 bg-white/5 p-4">
                                                <div className="h-2 w-16 rounded-full bg-white/20" />
                                                <div className="mt-4 text-2xl font-semibold text-white">
                                                    Rp 248 jt
                                                </div>
                                                <div className="mt-2 text-xs text-emerald-300">
                                                    +12,4% bulan ini
                                                </div>
                                            </div>
                                            <div className="rounded-xl border border-white/10 bg-white/5 p-4">
                                                <div className="h-2 w-14 rounded-full bg-white/20" />
                                                <div className="mt-4 text-2xl font-semibold text-white">
                                                    128
                                                </div>
                                                <div className="mt-2 text-xs text-slate-400">
                                                    invoice aktif
                                                </div>
                                            </div>
                                        </div>

                                        <div className="rounded-xl border border-white/10 bg-white/5 p-4">
                                            <div className="flex items-center justify-between">
                                                <div className="h-2.5 w-28 rounded-full bg-white/25" />
                                                <div className="h-2 w-12 rounded-full bg-blue-400/50" />
                                            </div>
                                            <div className="mt-6 flex h-32 items-end gap-2">
                                                {[
                                                    42, 64, 48, 78, 58, 92, 74,
                                                ].map((height, index) => (
                                                    <div
                                                        key={index}
                                                        className="flex-1 rounded-t-md bg-blue-500/80"
                                                        style={{
                                                            height: `${height}%`,
                                                        }}
                                                    />
                                                ))}
                                            </div>
                                        </div>

                                        <div className="space-y-3 rounded-xl border border-white/10 bg-white/5 p-4">
                                            {[
                                                [
                                                    'INV/2026/11/018',
                                                    'Rp 18.500.000',
                                                    'bg-emerald-400',
                                                ],
                                                [
                                                    'INV/2026/11/017',
                                                    'Rp 12.250.000',
                                                    'bg-blue-400',
                                                ],
                                                [
                                                    'INV/2026/11/016',
                                                    'Rp 9.800.000',
                                                    'bg-amber-400',
                                                ],
                                            ].map(([invoice, total, color]) => (
                                                <div
                                                    key={invoice}
                                                    className="flex items-center justify-between gap-3"
                                                >
                                                    <div className="flex items-center gap-2.5">
                                                        <span
                                                            className={`h-2 w-2 rounded-full ${color}`}
                                                        />
                                                        <span className="text-xs text-slate-300">
                                                            {invoice}
                                                        </span>
                                                    </div>
                                                    <span className="text-xs font-medium text-white">
                                                        {total}
                                                    </span>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="absolute -bottom-5 -left-5 hidden items-center gap-3 rounded-2xl border border-white/10 bg-slate-900 px-4 py-3 shadow-xl sm:flex">
                                <span className="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-400/15 text-emerald-300">
                                    <FileText
                                        aria-hidden="true"
                                        className="h-4 w-4"
                                    />
                                </span>
                                <div>
                                    <p className="text-xs font-semibold text-white">
                                        Invoice berhasil dikirim
                                    </p>
                                    <p className="text-[11px] text-slate-400">
                                        PDF & Excel siap diunduh
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section
                    aria-label="Keunggulan xInvoice"
                    className="border-b border-slate-200 bg-slate-50"
                >
                    <dl className="mx-auto grid max-w-7xl gap-8 px-6 py-10 sm:grid-cols-3">
                        <div className="flex items-start gap-4">
                            <span className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-600/10 text-blue-700">
                                <FileText
                                    aria-hidden="true"
                                    className="h-5 w-5"
                                />
                            </span>
                            <div>
                                <dt className="font-semibold">
                                    PDF & Excel otomatis
                                </dt>
                                <dd className="mt-1 text-sm text-slate-600">
                                    Arsip tagihan siap dibuat dari sumber data
                                    yang sama.
                                </dd>
                            </div>
                        </div>
                        <div className="flex items-start gap-4">
                            <span className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-600/10 text-violet-700">
                                <Workflow
                                    aria-hidden="true"
                                    className="h-5 w-5"
                                />
                            </span>
                            <div>
                                <dt className="font-semibold">
                                    Alur kerja ringkas
                                </dt>
                                <dd className="mt-1 text-sm text-slate-600">
                                    Dari customer dan PO hingga invoice terkirim
                                    dalam satu jalur.
                                </dd>
                            </div>
                        </div>
                        <div className="flex items-start gap-4">
                            <span className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-600/10 text-emerald-700">
                                <ShieldCheck
                                    aria-hidden="true"
                                    className="h-5 w-5"
                                />
                            </span>
                            <div>
                                <dt className="font-semibold">
                                    Setiap data terlacak
                                </dt>
                                <dd className="mt-1 text-sm text-slate-600">
                                    Ownership, permission, dan audit log menjaga
                                    kepatuhan operasional.
                                </dd>
                            </div>
                        </div>
                    </dl>
                </section>

                <section id="fitur" className="scroll-mt-24 bg-white py-20">
                    <div className="mx-auto max-w-7xl px-6">
                        <div className="max-w-2xl">
                            <Badge variant="secondary">Fitur utama</Badge>
                            <h2 className="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">
                                Semua yang dibutuhkan untuk penagihan harian.
                            </h2>
                            <p className="mt-4 text-lg leading-8 text-slate-600">
                                Fokus pada pekerjaan operasional, tanpa
                                memindahkan data antara banyak aplikasi.
                            </p>
                        </div>

                        <ul className="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            {features.map((feature) => (
                                <li key={feature.title} className="h-full">
                                    <article className="h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-blue-200 hover:shadow-lg motion-reduce:transform-none motion-reduce:transition-none">
                                        <span className="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600/10 text-blue-700">
                                            <feature.icon
                                                aria-hidden="true"
                                                className="h-5 w-5"
                                            />
                                        </span>
                                        <h3 className="mt-5 text-lg font-semibold">
                                            {feature.title}
                                        </h3>
                                        <p className="mt-2 text-sm leading-6 text-slate-600">
                                            {feature.description}
                                        </p>
                                    </article>
                                </li>
                            ))}
                        </ul>
                    </div>
                </section>

                <section
                    id="alur-kerja"
                    className="scroll-mt-24 border-y border-slate-200 bg-slate-50 py-20"
                >
                    <div className="mx-auto max-w-7xl px-6">
                        <div className="max-w-2xl">
                            <Badge variant="secondary">Alur kerja</Badge>
                            <h2 className="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">
                                Dari data master ke invoice dalam empat langkah.
                            </h2>
                        </div>

                        <ol className="mt-12 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                            {steps.map((step, index) => (
                                <li
                                    key={step.title}
                                    className="relative rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
                                >
                                    <div className="flex items-center justify-between">
                                        <span className="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-950 text-white">
                                            <step.icon
                                                aria-hidden="true"
                                                className="h-5 w-5"
                                            />
                                        </span>
                                        <span className="text-sm font-bold text-slate-300">
                                            {String(index + 1).padStart(2, '0')}
                                        </span>
                                    </div>
                                    <h3 className="mt-5 font-semibold">
                                        {step.title}
                                    </h3>
                                    <p className="mt-2 text-sm leading-6 text-slate-600">
                                        {step.description}
                                    </p>
                                </li>
                            ))}
                        </ol>
                    </div>
                </section>

                <section id="keamanan" className="scroll-mt-24 bg-white py-20">
                    <div className="mx-auto grid max-w-7xl gap-12 px-6 lg:grid-cols-2 lg:items-center">
                        <div>
                            <Badge variant="secondary">
                                Keamanan & kontrol
                            </Badge>
                            <h2 className="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">
                                Data pelanggan tetap milik tim Anda.
                            </h2>
                            <p className="mt-4 text-lg leading-8 text-slate-600">
                                Setiap pengguna bekerja dengan data miliknya
                                sendiri. Permission menentukan modul yang bisa
                                diakses, sedangkan audit trail mencatat
                                aktivitas penting.
                            </p>
                        </div>

                        <ul className="grid gap-4 sm:grid-cols-2">
                            {[
                                'Ownership per pengguna',
                                'Role & permission granular',
                                'Audit log aktivitas penting',
                                'Dokumen tersimpan aman',
                            ].map((item) => (
                                <li
                                    key={item}
                                    className="flex min-h-12 items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium"
                                >
                                    <CircleCheck
                                        aria-hidden="true"
                                        className="h-5 w-5 shrink-0 text-emerald-600"
                                    />
                                    {item}
                                </li>
                            ))}
                        </ul>
                    </div>
                </section>

                <section className="bg-slate-950 px-6 py-20 text-white">
                    <div className="mx-auto max-w-7xl overflow-hidden rounded-3xl bg-[linear-gradient(in_oklab,#1d4ed8_0%,#7c3aed_100%)] p-8 shadow-2xl shadow-blue-950/30 sm:p-12 lg:p-16">
                        <div className="flex flex-col items-start justify-between gap-8 lg:flex-row lg:items-center">
                            <div className="max-w-2xl">
                                <h2 className="text-3xl font-bold tracking-tight sm:text-4xl">
                                    Siap membuat invoice lebih rapi hari ini?
                                </h2>
                                <p className="mt-4 text-lg leading-8 text-blue-100">
                                    Masuk ke workspace xInvoice dan lanjutkan
                                    penagihan tanpa spreadsheet yang tercecer.
                                </p>
                            </div>

                            {auth.user ? (
                                <Button
                                    asChild
                                    size="lg"
                                    className="h-11 bg-white px-5 text-blue-700 hover:bg-blue-50"
                                >
                                    <Link href={route('dashboard')}>
                                        Buka dashboard
                                        <ArrowRight aria-hidden="true" />
                                    </Link>
                                </Button>
                            ) : (
                                canLogin && (
                                    <Button
                                        asChild
                                        size="lg"
                                        className="h-11 bg-white px-5 text-blue-700 hover:bg-blue-50"
                                    >
                                        <Link href={route('login')}>
                                            Masuk ke xInvoice
                                            <ArrowRight aria-hidden="true" />
                                        </Link>
                                    </Button>
                                )
                            )}
                        </div>
                    </div>
                </section>
            </main>

            <footer className="border-t border-slate-200 bg-white">
                <div className="mx-auto flex max-w-7xl flex-col gap-5 px-6 py-8 sm:flex-row sm:items-center sm:justify-between">
                    <div className="flex items-center gap-2.5">
                        <ApplicationLogo className="h-8 w-8 text-blue-600" />
                        <div>
                            <p className="font-semibold">xInvoice</p>
                            <p className="text-sm text-slate-500">
                                Invoice management untuk tim modern.
                            </p>
                        </div>
                    </div>
                    <p className="text-sm text-slate-500">
                        Customer, PO, invoice, dan laporan dalam satu workspace.
                    </p>
                </div>
            </footer>
        </div>
    );
}
