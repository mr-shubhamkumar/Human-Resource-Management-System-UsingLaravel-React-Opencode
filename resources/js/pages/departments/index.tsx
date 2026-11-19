import { Head, Link } from '@inertiajs/react';
import { Pencil } from 'lucide-react';
import DepartmentController from '@/actions/App/Http/Controllers/HRM/DepartmentController';
import DataTable, { type Paginator } from '@/components/hrm/data-table';
import DeleteAction from '@/components/hrm/delete-action';
import PageHeader from '@/components/hrm/page-header';
import StatusBadge from '@/components/hrm/status-badge';
import { Button } from '@/components/ui/button';
import { create, edit, index } from '@/routes/departments';

type Department = {
    id: number;
    name: string;
    description: string | null;
    is_approved: boolean;
    employees_count: number;
    created_at: string;
};

type Props = {
    departments: Paginator<Department>;
    filters: { search: string };
};

export default function DepartmentsIndex({ departments, filters }: Props) {
    return (
        <>
            <Head title="Departments" />
            <div className="space-y-6 p-4">
                <PageHeader
                    title="Departments"
                    description="Organize employees into teams and business units."
                    action={
                        <Button asChild>
                            <Link href={create()}>Add department</Link>
                        </Button>
                    }
                />

                <DataTable
                    columns={[
                        {
                            key: 'name',
                            label: 'Department',
                            render: (department) => (
                                <div className="font-medium">
                                    {department.name}
                                </div>
                            ),
                        },
                        {
                            key: 'description',
                            label: 'Description',
                            render: (department) =>
                                department.description ?? '—',
                        },
                        {
                            key: 'employees_count',
                            label: 'Employees',
                            render: (department) => department.employees_count,
                        },
                        {
                            key: 'is_approved',
                            label: 'Approval',
                            render: (department) => (
                                <StatusBadge
                                    status={
                                        department.is_approved
                                            ? 'approved'
                                            : 'pending'
                                    }
                                />
                            ),
                        },
                        {
                            key: 'created_at',
                            label: 'Created',
                            render: (department) =>
                                department.created_at.slice(0, 10),
                        },
                        {
                            key: 'actions',
                            label: 'Actions',
                            className: 'text-right',
                            render: (department) => (
                                <div className="flex justify-end gap-1">
                                    <Button asChild variant="ghost" size="icon">
                                        <Link
                                            href={edit(department.id)}
                                            aria-label="Edit department"
                                        >
                                            <Pencil className="size-4" />
                                        </Link>
                                    </Button>
                                    <DeleteAction
                                        action={DepartmentController.destroy.form(
                                            department.id,
                                        )}
                                        title="Delete department"
                                        description={`Delete ${department.name}? Employees assigned to it will also be removed.`}
                                    />
                                </div>
                            ),
                        },
                    ]}
                    rows={departments.data}
                    rowKey={(department) => department.id}
                    pagination={departments}
                    search={filters.search}
                    searchRoute={index().url}
                    emptyMessage="No departments found."
                />
            </div>
        </>
    );
}

DepartmentsIndex.layout = {
    breadcrumbs: [{ title: 'Departments', href: index() }],
};
