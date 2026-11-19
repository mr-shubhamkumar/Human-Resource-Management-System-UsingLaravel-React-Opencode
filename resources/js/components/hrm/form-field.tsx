import type { ReactNode } from 'react';
import InputError from '@/components/input-error';
import { Label } from '@/components/ui/label';

type Props = {
    label: string;
    name: string;
    error?: string;
    hint?: string;
    children: ReactNode;
};

export default function FormField({
    label,
    name,
    error,
    hint,
    children,
}: Props) {
    return (
        <div className="grid gap-2">
            <Label htmlFor={name}>{label}</Label>
            {children}
            {hint && (
                <p className="text-muted-foreground text-xs">{hint}</p>
            )}
            <InputError message={error} />
        </div>
    );
}
