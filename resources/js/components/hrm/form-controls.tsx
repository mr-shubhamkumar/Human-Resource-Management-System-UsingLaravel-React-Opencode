import type { ComponentProps } from 'react';
import { cn } from '@/lib/utils';

const CONTROL_CLASSES =
    'border-input placeholder:text-muted-foreground flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50';

export function SelectInput({
    className,
    children,
    ...props
}: ComponentProps<'select'>) {
    return (
        <select
            data-slot="select"
            className={cn(CONTROL_CLASSES, className)}
            {...props}
        >
            {children}
        </select>
    );
}

export function TextareaInput({
    className,
    ...props
}: ComponentProps<'textarea'>) {
    return (
        <textarea
            data-slot="textarea"
            className={cn(
                CONTROL_CLASSES,
                'min-h-20 py-2',
                className,
            )}
            {...props}
        />
    );
}
