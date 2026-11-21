import { cn } from 'cn';
import { HTMLAttributes } from 'react';

export default function InputError({
    message,
    className = '',
    ...props
}: HTMLAttributes<HTMLParagraphElement> & { message?: string }) {
    return message ? (
        <p className={cn('text-destructive text-sm', className)} {...props}>
            {message}
        </p>
    ) : null;
}
