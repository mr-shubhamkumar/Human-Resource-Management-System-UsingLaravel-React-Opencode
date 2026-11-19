import type { ReactNode } from 'react';

type Props = {
    title: string;
    description?: string;
    action?: ReactNode;
};

export default function PageHeader({ title, description, action }: Props) {
    return (
        <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 className="text-2xl font-semibold tracking-tight">{title}</h1>
                {description && (
                    <p className="text-muted-foreground mt-1 text-sm">
                        {description}
                    </p>
                )}
            </div>
            {action}
        </div>
    );
}
