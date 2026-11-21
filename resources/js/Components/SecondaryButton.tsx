import { Button } from '@/Components/ui/button';
import { cn } from 'cn';
import { ButtonHTMLAttributes } from 'react';

export default function SecondaryButton({
    type = 'button',
    className = '',
    disabled,
    children,
    ...props
}: ButtonHTMLAttributes<HTMLButtonElement>) {
    return (
        <Button
            type={type}
            variant="outline"
            size="lg"
            disabled={disabled}
            className={cn('h-10 px-4', className)}
            {...props}
        >
            {children}
        </Button>
    );
}
