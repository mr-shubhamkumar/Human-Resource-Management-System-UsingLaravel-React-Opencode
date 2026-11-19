import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';

type Variant = 'default' | 'secondary' | 'destructive' | 'outline';

const STATUS_VARIANTS: Record<string, Variant> = {
    active: 'default',
    approved: 'default',
    present: 'default',
    completed: 'default',
    inactive: 'secondary',
    planning: 'secondary',
    pending: 'outline',
    late: 'outline',
    terminated: 'destructive',
    rejected: 'destructive',
    absent: 'destructive',
    in_progress: 'secondary',
};

export default function StatusBadge({
    status,
    className,
}: {
    status: string;
    className?: string;
}) {
    const variant = STATUS_VARIANTS[status.toLowerCase()] ?? 'outline';

    return (
        <Badge variant={variant} className={cn('capitalize', className)}>
            {status.replaceAll('_', ' ')}
        </Badge>
    );
}
