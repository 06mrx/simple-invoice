import { Label } from '@/Components/ui/label';
import { cn } from 'cn';
import { LabelHTMLAttributes } from 'react';

export default function InputLabel({
    value,
    className = '',
    children,
    ...props
}: LabelHTMLAttributes<HTMLLabelElement> & { value?: string }) {
    return (
        <Label className={cn('text-foreground mb-1', className)} {...props}>
            {value ? value : children}
        </Label>
    );
}
