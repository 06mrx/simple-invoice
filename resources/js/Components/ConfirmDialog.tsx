import { Button } from '@/Components/ui/button';
import { ReactNode, useEffect, useRef } from 'react';

type Props = {
    open: boolean;
    title: string;
    description?: ReactNode;
    confirmLabel?: string;
    cancelLabel?: string;
    processing?: boolean;
    destructive?: boolean;
    onConfirm: () => void;
    onClose: () => void;
};

export default function ConfirmDialog({
    open,
    title,
    description,
    confirmLabel = 'Konfirmasi',
    cancelLabel = 'Batal',
    processing = false,
    destructive = false,
    onConfirm,
    onClose,
}: Props) {
    const ref = useRef<HTMLDialogElement>(null);

    useEffect(() => {
        const dialog = ref.current;
        if (!dialog) return;

        if (open && !dialog.open) {
            dialog.showModal();
        }

        if (!open && dialog.open) {
            dialog.close();
        }
    }, [open]);

    return (
        <dialog
            ref={ref}
            onClose={onClose}
            onCancel={(e) => {
                e.preventDefault();
                if (!processing) onClose();
            }}
            className="bg-card text-card-foreground fixed inset-0 m-auto w-full max-w-md rounded-lg border p-0 shadow-xl backdrop:bg-black/50"
        >
            <div className="space-y-4 p-6">
                <div className="space-y-2">
                    <h2 className="text-lg font-semibold">{title}</h2>
                    {description && (
                        <div className="text-muted-foreground text-sm">
                            {description}
                        </div>
                    )}
                </div>
                <div className="flex justify-end gap-2">
                    <Button
                        type="button"
                        variant="outline"
                        onClick={onClose}
                        disabled={processing}
                        autoFocus
                    >
                        {cancelLabel}
                    </Button>
                    <Button
                        type="button"
                        variant={destructive ? 'destructive' : 'default'}
                        onClick={onConfirm}
                        disabled={processing}
                    >
                        {confirmLabel}
                    </Button>
                </div>
            </div>
        </dialog>
    );
}
