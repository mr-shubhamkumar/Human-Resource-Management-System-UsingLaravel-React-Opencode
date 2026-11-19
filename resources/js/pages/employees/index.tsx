import { Head, Link } from '@inertiajs/react';
import { Pencil } from 'lucide-react';
import EmployeeController from '@/actions/App/Http/Controllers/HRM/EmployeeController';
import DataTable, {
    type Paginator,
} from '@/components/hrm/data-table';
import DeleteAction from '@/components/hrm/delete-action';
import PageHeader from '@/components/hrm/page-header';
import StatusBadge from '@/components/hrm/status-badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { index, create, edit } from '@/routes/employees';

type Employee = {
    id: number;
    employee_id: string;
    first_name: string;
    last_name: string;
    email: string;
    status: string;
    is_approved: boolean;
    hire_date: string;
    profile_photo_url: string | null;
    department: { name: string } | null;
    position: { title: string } | null;
};

type Props = {
    employees: Paginator<Employee>;
    filters: { search: string };
    nextEmployeeId: string;
};

export default function EmployeesIndex({
    employees,
    filters,
    nextEmployeeId,
}: Props) {
    return (
        <>
            <Head title="Employees" />
            <div className="space-y-6 p-4">
                <PageHeader
                    title="Employees"
                    description={`Manage your workforce records. Next ID: ${nextEmployeeId}`}
                    action={
                        <Button asChild>
                            <Link href={create()}>Add employee</Link>
                        </Button>
                    }
                />

                <DataTable
                    columns={[
                        {
                            key: 'profile_photo',
                            label: 'Photo',
                            render: (employee) => (
                                <Avatar className="size-9">
                                    {employee.profile_photo_url && (
                                        <AvatarImage
                                            src={employee.profile_photo_url}
                                            alt={`${employee.first_name} ${employee.last_name}`}
                                        />
                                    )}
                                    <AvatarFallback>
                                        {employee.first_name.charAt(0)}
                                        {employee.last_name.charAt(0)}
                                    </AvatarFallback>
                                </Avatar>
                            ),
                        },
                        { key: 'employee_id', label: 'Employee ID' },
                        {
                            key: 'name',
                            label: 'Name',
                            render: (employee) => (
                                <div className="font-medium">
                                    {employee.first_name} {employee.last_name}
                                </div>
                            ),
                        },
                        { key: 'email', label: 'Email' },
                        {
                            key: 'department',
                            label: 'Department',
                            render: (employee) =>
                                employee.department?.name ?? '—',
                        },
                        {
                            key: 'position',
                            label: 'Position',
                            render: (employee) =>
                                employee.position?.title ?? '—',
                        },
                        {
                            key: 'status',
                            label: 'Status',
                            render: (employee) => (
                                <StatusBadge status={employee.status} />
                            ),
                        },
                        {
                            key: 'is_approved',
                            label: 'Approval',
                            render: (employee) => (
                                <StatusBadge
                                    status={
                                        employee.is_approved
                                            ? 'approved'
                                            : 'pending'
                                    }
                                />
                            ),
                        },
                        {
                            key: 'hire_date',
                            label: 'Hired',
                            render: (employee) =>
                                employee.hire_date.slice(0, 10),
                        },
                        {
                            key: 'actions',
                            label: 'Actions',
                            className: 'text-right',
                            render: (employee) => (
                                <div className="flex justify-end gap-1">
                                    <Button asChild variant="ghost" size="icon">
                                        <Link
                                            href={edit(employee.id)}
                                            aria-label="Edit employee"
                                        >
                                            <Pencil className="size-4" />
                                        </Link>
                                    </Button>
                                    <DeleteAction
                                        action={EmployeeController.destroy.form(
                                            employee.id,
                                        )}
                                        title="Delete employee"
                                        description={`Delete ${employee.first_name} ${employee.last_name} and all related records? This cannot be undone.`}
                                    />
                                </div>
                            ),
                        },
                    ]}
                    rows={employees.data}
                    rowKey={(employee) => employee.id}
                    pagination={employees}
                    search={filters.search}
                    searchRoute={index().url}
                    emptyMessage="No employees found. Try a different search term."
                />
            </div>
        </>
    );
}

EmployeesIndex.layout = {
    breadcrumbs: [
        { title: 'Employees', href: index() },
    ],
};
