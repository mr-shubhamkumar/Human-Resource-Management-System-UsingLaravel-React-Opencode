import { Head, Link } from '@inertiajs/react';
import { Pencil } from 'lucide-react';
import AttendanceController from '@/actions/App/Http/Controllers/HRM/AttendanceController';
import DataTable, { type Paginator } from '@/components/hrm/data-table';
import DeleteAction from '@/components/hrm/delete-action';
import PageHeader from '@/components/hrm/page-header';
import StatusBadge from '@/components/hrm/status-badge';
import { Button } from '@/components/ui/button';
import { create, edit, index } from '@/routes/attendances';

type Attendance = {
    id: number;
    date: string;
    check_in: string | null;
    check_out: string | null;
    status: string;
    employee: {
        first_name: string;
        last_name: string;
        employee_id: string;
    } | null;
};

type Props = {
    attendances: Paginator<Attendance>;
    filters: { search: string };
};

export default function AttendancesIndex({ attendances, filters }: Props) {
    return (
        <>
            <Head title="Attendance" />
            <div className="space-y-6 p-4">
                <PageHeader
                    title="Attendance"
                    description="Daily check-in and check-out records."
                    action={
                        <Button asChild>
                            <Link href={create()}>Add attendance</Link>
                        </Button>
                    }
                />

                <DataTable
                    columns={[
                        {
                            key: 'date',
                            label: 'Date',
                            render: (attendance) =>
                                attendance.date.slice(0, 10),
                        },
                        {
                            key: 'employee',
                            label: 'Employee',
                            render: (attendance) =>
                                attendance.employee
                                    ? `${attendance.employee.first_name} ${attendance.employee.last_name} (${attendance.employee.employee_id})`
                                    : '—',
                        },
                        {
                            key: 'check_in',
                            label: 'Check in',
                            render: (attendance) =>
                                attendance.check_in?.slice(0, 5) ?? '—',
                        },
                        {
                            key: 'check_out',
                            label: 'Check out',
                            render: (attendance) =>
                                attendance.check_out?.slice(0, 5) ?? '—',
                        },
                        {
                            key: 'status',
                            label: 'Status',
                            render: (attendance) => (
                                <StatusBadge status={attendance.status} />
                            ),
                        },
                        {
                            key: 'actions',
                            label: 'Actions',
                            className: 'text-right',
                            render: (attendance) => (
                                <div className="flex justify-end gap-1">
                                    <Button asChild variant="ghost" size="icon">
                                        <Link
                                            href={edit(attendance.id)}
                                            aria-label="Edit attendance"
                                        >
                                            <Pencil className="size-4" />
                                        </Link>
                                    </Button>
                                    <DeleteAction
                                        action={AttendanceController.destroy.form(
                                            attendance.id,
                                        )}
                                        title="Delete attendance"
                                    />
                                </div>
                            ),
                        },
                    ]}
                    rows={attendances.data}
                    rowKey={(attendance) => attendance.id}
                    pagination={attendances}
                    search={filters.search}
                    searchRoute={index().url}
                    emptyMessage="No attendance records found."
                />
            </div>
        </>
    );
}

AttendancesIndex.layout = {
    breadcrumbs: [{ title: 'Attendance', href: index() }],
};
