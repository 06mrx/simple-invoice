import { cn } from 'cn';
import * as React from 'react';

const Input = React.forwardRef<HTMLInputElement, React.ComponentProps<'input'>>(
    ({ className, type, ...props }, ref) => (
        <input
            ref={ref}
            type={type}
            data-slot="input"
            className={cn(
                'border-input bg-background hover:bg-accent/30 focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 dark:bg-input/30 file:text-foreground placeholder:text-muted-foreground h-10 w-full min-w-0 rounded-lg border px-3 py-2 text-sm shadow-sm transition-colors outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium focus-visible:ring-3 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:ring-3 md:text-sm',
                className,
            )}
            {...props}
        />
    ),
);
Input.displayName = 'Input';

export { Input };
