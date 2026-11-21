import { cn } from 'cn';
import { HTMLAttributes } from 'react';

export default function InputHint({
    className = '',
    children,
    ...props
}: HTMLAttributes<HTMLParagraphElement>) {
    return (
        <p
            className={cn('text-muted-foreground mt-1 text-xs', className)}
            {...props}
        >
            {children}
        </p>
    );
}
