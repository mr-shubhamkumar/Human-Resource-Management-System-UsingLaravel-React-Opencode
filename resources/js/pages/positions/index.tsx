import { Head, Link } from '@inertiajs/react';
import { Pencil } from 'lucide-react';
import PositionController from '@/actions/App/Http/Controllers/HRM/PositionController';
import DataTable, { type Paginator } from '@/components/hrm/data-table';
import DeleteAction from '@/components/hrm/delete-action';
import PageHeader from '@/components/hrm/page-header';
import { Button } from '@/components/ui/button';
import { create, edit, index } from '@/routes/positions';

type Position = {
    id: number;
    title: string;
    description: string | null;
    salary_min: string;
    salary_max: string;
    employees_count: number;
    department: { name: string } | null;
};

type Props = {
    positions: Paginator<Position>;
    filters: { search: string };
};

export default function PositionsIndex({ positions, filters }: Props) {
    return (
        <>
            <Head title="Positions" />
            <div className="space-y-6 p-4">
                <PageHeader
                    title="Positions"
                    description="Job titles, departments and salary ranges."
                    action={
                        <Button asChild>
                            <Link href={create()}>Add position</Link>
                        </Button>
                    }
                />

                <DataTable
                    columns={[
                        {
                            key: 'title',
                            label: 'Title',
                            render: (position) => (
                                <div className="font-medium">
                                    {position.title}
                                </div>
                            ),
                        },
                        {
                            key: 'department',
                            label: 'Department',
                            render: (position) =>
                                position.department?.name ?? '—',
                        },
                        {
                            key: 'salary',
                            label: 'Salary range',
                            render: (position) =>
                                `${Number(position.salary_min).toLocaleString()} – ${Number(position.salary_max).toLocaleString()}`,
                        },
                        {
                            key: 'employees_count',
                            label: 'Employees',
                            render: (position) => position.employees_count,
                        },
                        {
                            key: 'description',
                            label: 'Description',
                            render: (position) =>
                                position.description ?? '—',
                        },
                        {
                            key: 'actions',
                            label: 'Actions',
                            className: 'text-right',
                            render: (position) => (
                                <div className="flex justify-end gap-1">
                                    <Button asChild variant="ghost" size="icon">
                                        <Link
                                            href={edit(position.id)}
                                            aria-label="Edit position"
                                        >
                                            <Pencil className="size-4" />
                                        </Link>
                                    </Button>
                                    <DeleteAction
                                        action={PositionController.destroy.form(
                                            position.id,
                                        )}
                                        title="Delete position"
                                        description={`Delete ${position.title}? Employees assigned to it will also be removed.`}
                                    />
                                </div>
                            ),
                        },
                    ]}
                    rows={positions.data}
                    rowKey={(position) => position.id}
                    pagination={positions}
                    search={filters.search}
                    searchRoute={index().url}
                    emptyMessage="No positions found."
                />
            </div>
        </>
    );
}

PositionsIndex.layout = {
    breadcrumbs: [{ title: 'Positions', href: index() }],
};
