import { Link, router } from '@inertiajs/react';
import { Search } from 'lucide-react';
import { type FormEvent, type ReactNode, useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { cn } from '@/lib/utils';

export type DataTableColumn<T> = {
    key: string;
    label: string;
    render?: (row: T) => ReactNode;
    className?: string;
};

export type PaginationData = {
    links: Array<{ url: string | null; label: string; active: boolean }>;
    from: number | null;
    to: number | null;
    total: number;
};

export type Paginator<T> = PaginationData & {
    data: T[];
    current_page: number;
    last_page: number;
};

type Props<T> = {
    columns: Array<DataTableColumn<T>>;
    rows: T[];
    rowKey: (row: T) => string | number;
    pagination?: PaginationData;
    search?: string;
    searchRoute?: string;
    emptyMessage?: string;
};

function decodeLabel(label: string): string {
    return label
        .replaceAll('&laquo;', '«')
        .replaceAll('&raquo;', '»')
        .replaceAll('&nbsp;', ' ')
        .trim();
}

export default function DataTable<T>({
    columns,
    rows,
    rowKey,
    pagination,
    search,
    searchRoute,
    emptyMessage = 'No records found.',
}: Props<T>) {
    const [term, setTerm] = useState(search ?? '');

    function handleSearch(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();

        if (!searchRoute) {
            return;
        }

        router.get(
            searchRoute,
            term.trim() === '' ? {} : { search: term.trim() },
            { preserveState: true, preserveScroll: true },
        );
    }

    return (
        <div className="space-y-4">
            {searchRoute && (
                <form onSubmit={handleSearch} className="flex gap-2 sm:max-w-sm">
                    <div className="relative flex-1">
                        <Search className="text-muted-foreground absolute top-1/2 left-3 size-4 -translate-y-1/2" />
                        <Input
                            name="search"
                            value={term}
                            onChange={(event) => setTerm(event.target.value)}
                            placeholder="Search records..."
                            className="pl-9"
                            aria-label="Search records"
                        />
                    </div>
                    <Button type="submit" variant="secondary">
                        Search
                    </Button>
                </form>
            )}

            <div className="overflow-x-auto rounded-lg border">
                <table className="w-full text-left text-sm">
                    <thead className="bg-muted/50 text-muted-foreground">
                        <tr>
                            {columns.map((column) => (
                                <th
                                    key={column.key}
                                    className={cn(
                                        'px-4 py-3 font-medium whitespace-nowrap',
                                        column.className,
                                    )}
                                >
                                    {column.label}
                                </th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {rows.length === 0 ? (
                            <tr>
                                <td
                                    className="text-muted-foreground px-4 py-8 text-center"
                                    colSpan={columns.length}
                                >
                                    {emptyMessage}
                                </td>
                            </tr>
                        ) : (
                            rows.map((row) => (
                                <tr
                                    key={rowKey(row)}
                                    className="border-t transition-colors hover:bg-muted/40"
                                >
                                    {columns.map((column) => (
                                        <td
                                            key={column.key}
                                            className={cn('px-4 py-3', column.className)}
                                        >
                                            {column.render
                                                ? column.render(row)
                                                : String(
                                                      (row as Record<string, unknown>)[
                                                          column.key
                                                      ] ?? '—',
                                                  )}
                                        </td>
                                    ))}
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>

            {pagination && pagination.total > 0 && (
                <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p className="text-muted-foreground text-sm">
                        Showing {pagination.from ?? 0}–{pagination.to ?? 0} of{' '}
                        {pagination.total}
                    </p>
                    <nav
                        className="flex flex-wrap gap-1"
                        aria-label="Pagination"
                    >
                        {pagination.links.map((link, index) =>
                            link.url ? (
                                <Button
                                    key={`${link.label}-${index}`}
                                    asChild
                                    size="sm"
                                    variant={
                                        link.active ? 'default' : 'outline'
                                    }
                                >
                                    <Link href={link.url} preserveScroll>
                                        {decodeLabel(link.label)}
                                    </Link>
                                </Button>
                            ) : (
                                <Button
                                    key={`${link.label}-${index}`}
                                    size="sm"
                                    variant="outline"
                                    disabled
                                >
                                    {decodeLabel(link.label)}
                                </Button>
                            ),
                        )}
                    </nav>
                </div>
            )}
        </div>
    );
}
