import { Button } from '@/Components/ui/button';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { formatDate } from '@/lib/utils';
import { Head, Link } from '@inertiajs/react';

type User = { id: number; name: string };
type Audit = {
    id: string | number;
    event: string;
    auditable_type: string;
    auditable_id: string | number;
    old_values?: Record<string, unknown> | null;
    new_values?: Record<string, unknown> | null;
    user?: User | null;
    created_at: string;
};

export default function AuditShow({ audit }: { audit: Audit }) {
    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold">Detail Audit</h2>}
        >
            <Head title="Detail Audit" />
            <div className="py-6">
                <div className="mx-auto max-w-5xl space-y-4 px-4 sm:px-6 lg:px-8">
                    <div className="bg-card rounded-md border p-6">
                        <div className="grid gap-2 text-sm">
                            <div>
                                <span className="font-medium">Waktu:</span>{' '}
                                {formatDate(audit.created_at)}
                            </div>
                            <div>
                                <span className="font-medium">Event:</span>{' '}
                                {audit.event}
                            </div>
                            <div>
                                <span className="font-medium">Model:</span>{' '}
                                {audit.auditable_type} #{audit.auditable_id}
                            </div>
                            <div>
                                <span className="font-medium">User:</span>{' '}
                                {audit.user?.name ?? '-'}
                            </div>
                        </div>
                    </div>

                    <div className="grid gap-4 md:grid-cols-2">
                        <div className="bg-card rounded-md border p-6">
                            <h3 className="mb-2 font-semibold">Old Values</h3>
                            <pre className="overflow-auto text-xs">
                                {JSON.stringify(
                                    audit.old_values ?? {},
                                    null,
                                    2,
                                )}
                            </pre>
                        </div>
                        <div className="bg-card rounded-md border p-6">
                            <h3 className="mb-2 font-semibold">New Values</h3>
                            <pre className="overflow-auto text-xs">
                                {JSON.stringify(
                                    audit.new_values ?? {},
                                    null,
                                    2,
                                )}
                            </pre>
                        </div>
                    </div>

                    <Button variant="outline" asChild>
                        <Link href={route('admin.audit.index')}>Kembali</Link>
                    </Button>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
