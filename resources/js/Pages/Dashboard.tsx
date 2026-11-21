import { Button } from '@/Components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function Dashboard() {
    const cards = [
        {
            title: 'Data Bayi',
            description: 'Kelola data bayi dan restore.',
            href: route('bayi.index'),
        },
        {
            title: 'Users',
            description: 'Kelola pengguna dan role.',
            href: route('admin.users.index'),
        },
        {
            title: 'Roles',
            description: 'Kelola role dan permission.',
            href: route('admin.roles.index'),
        },
        {
            title: 'Audit Log',
            description: 'Riwayat aktivitas sistem.',
            href: route('admin.audit.index'),
        },
    ];

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold">Dashboard</h2>}
        >
            <Head title="Dashboard" />

            <div className="space-y-6">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight">
                        Selamat datang
                    </h1>
                    <p className="text-muted-foreground">
                        Pilih modul untuk mulai bekerja.
                    </p>
                </div>

                <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    {cards.map((card) => (
                        <Card key={card.title}>
                            <CardHeader>
                                <CardTitle>{card.title}</CardTitle>
                                <CardDescription>
                                    {card.description}
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <Button asChild variant="outline">
                                    <Link href={card.href}>Buka</Link>
                                </Button>
                            </CardContent>
                        </Card>
                    ))}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
