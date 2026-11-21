import { Button } from '@/Components/ui/button';
import { cn } from 'cn';
import { ButtonHTMLAttributes } from 'react';

export default function PrimaryButton({
    className = '',
    disabled,
    children,
    ...props
}: ButtonHTMLAttributes<HTMLButtonElement>) {
    return (
        <Button
            size="lg"
            disabled={disabled}
            className={cn('h-10 px-4', className)}
            {...props}
        >
            {children}
        </Button>
    );
}
