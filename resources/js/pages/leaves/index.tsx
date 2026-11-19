import { Head, Link } from '@inertiajs/react';
import { Pencil } from 'lucide-react';
import LeaveController from '@/actions/App/Http/Controllers/HRM/LeaveController';
import DataTable, { type Paginator } from '@/components/hrm/data-table';
import DeleteAction from '@/components/hrm/delete-action';
import PageHeader from '@/components/hrm/page-header';
import StatusBadge from '@/components/hrm/status-badge';
import { Button } from '@/components/ui/button';
import { create, edit, index } from '@/routes/leaves';

type Leave = {
    id: number;
    start_date: string;
    end_date: string;
    reason: string;
    status: string;
    employee: {
        first_name: string;
        last_name: string;
        employee_id: string;
    } | null;
    leave_type: { name: string } | null;
    approved_by: { name: string } | null;
};

type Props = {
    leaves: Paginator<Leave>;
    filters: { search: string };
};

export default function LeavesIndex({ leaves, filters }: Props) {
    return (
        <>
            <Head title="Leaves" />
            <div className="space-y-6 p-4">
                <PageHeader
                    title="Leave Requests"
                    description="Review and manage employee leave applications."
                    action={
                        <Button asChild>
                            <Link href={create()}>Add leave</Link>
                        </Button>
                    }
                />

                <DataTable
                    columns={[
                        {
                            key: 'employee',
                            label: 'Employee',
                            render: (leave) =>
                                leave.employee
                                    ? `${leave.employee.first_name} ${leave.employee.last_name} (${leave.employee.employee_id})`
                                    : '—',
                        },
                        {
                            key: 'leave_type',
                            label: 'Leave type',
                            render: (leave) => leave.leave_type?.name ?? '—',
                        },
                        {
                            key: 'period',
                            label: 'Period',
                            render: (leave) =>
                                `${leave.start_date.slice(0, 10)} – ${leave.end_date.slice(0, 10)}`,
                        },
                        {
                            key: 'reason',
                            label: 'Reason',
                            render: (leave) => (
                                <span className="line-clamp-2 max-w-xs">
                                    {leave.reason}
                                </span>
                            ),
                        },
                        {
                            key: 'status',
                            label: 'Status',
                            render: (leave) => (
                                <StatusBadge status={leave.status} />
                            ),
                        },
                        {
                            key: 'approved_by',
                            label: 'Approved by',
                            render: (leave) => leave.approved_by?.name ?? '—',
                        },
                        {
                            key: 'actions',
                            label: 'Actions',
                            className: 'text-right',
                            render: (leave) => (
                                <div className="flex justify-end gap-1">
                                    <Button asChild variant="ghost" size="icon">
                                        <Link
                                            href={edit(leave.id)}
                                            aria-label="Edit leave"
                                        >
                                            <Pencil className="size-4" />
                                        </Link>
                                    </Button>
                                    <DeleteAction
                                        action={LeaveController.destroy.form(
                                            leave.id,
                                        )}
                                        title="Delete leave request"
                                    />
                                </div>
                            ),
                        },
                    ]}
                    rows={leaves.data}
                    rowKey={(leave) => leave.id}
                    pagination={leaves}
                    search={filters.search}
                    searchRoute={index().url}
                    emptyMessage="No leave requests found."
                />
            </div>
        </>
    );
}

LeavesIndex.layout = {
    breadcrumbs: [{ title: 'Leaves', href: index() }],
};
