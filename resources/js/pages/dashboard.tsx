import { Head } from '@inertiajs/react';
import {
    BriefcaseBusiness,
    Building2,
    CalendarDays,
    ClipboardCheck,
    Users,
} from 'lucide-react';
import Chart from '@/components/hrm/chart';
import StatusBadge from '@/components/hrm/status-badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard } from '@/routes';

type Props = {
    stats: {
        total_employees: number;
        employees_on_leave: number;
        department_count: number;
        pending_employees: number;
        pending_leaves: number;
    };
    charts: {
        employees_by_department: { labels: string[]; data: number[] };
        attendance: {
            labels: string[];
            present: number[];
            late: number[];
            absent: number[];
        };
        leave_status: { labels: string[]; data: number[] };
        payroll_trend: { labels: string[]; data: number[] };
    };
    recent_projects: Array<{
        id: number;
        title: string;
        client: string;
        progress: number;
        status: string;
        start_date: string;
        owner?: string;
    }>;
};

const STAT_CARDS = [
    {
        key: 'total_employees',
        label: 'Total employees',
        icon: Users,
        tone: 'text-blue-500',
    },
    {
        key: 'employees_on_leave',
        label: 'Employees on leave',
        icon: CalendarDays,
        tone: 'text-amber-500',
    },
    {
        key: 'department_count',
        label: 'Departments',
        icon: Building2,
        tone: 'text-emerald-500',
    },
] as const;

export default function Dashboard({
    stats,
    charts,
    recent_projects,
}: Props) {
    return (
        <>
            <Head title="Dashboard" />

            <div className="space-y-6 p-4">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        HR Overview
                    </h1>
                    <p className="text-muted-foreground mt-1 text-sm">
                        Real-time snapshot of your workforce and approvals.
                    </p>
                </div>

                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                    {STAT_CARDS.map(({ key, label, icon: Icon, tone }) => (
                        <Card key={key}>
                            <CardContent className="flex items-center justify-between">
                                <div>
                                    <p className="text-muted-foreground text-xs font-medium uppercase">
                                        {label}
                                    </p>
                                    <p className="mt-2 text-3xl font-semibold">
                                        {stats[key]}
                                    </p>
                                </div>
                                <Icon className={`size-8 ${tone}`} />
                            </CardContent>
                        </Card>
                    ))}

                    <Card>
                        <CardContent className="flex items-center justify-between">
                            <div>
                                <p className="text-muted-foreground text-xs font-medium uppercase">
                                    Pending approvals
                                </p>
                                <p className="mt-2 text-3xl font-semibold">
                                    {stats.pending_employees +
                                        stats.pending_leaves}
                                </p>
                                <p className="text-muted-foreground mt-1 text-xs">
                                    {stats.pending_employees} employees ·{' '}
                                    {stats.pending_leaves} leaves
                                </p>
                            </div>
                            <ClipboardCheck className="size-8 text-rose-500" />
                        </CardContent>
                    </Card>
                </div>

                <div className="grid gap-4 lg:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Attendance (last 30 days)</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Chart
                                type="line"
                                labels={charts.attendance.labels}
                                datasets={[
                                    {
                                        label: 'Present',
                                        data: charts.attendance.present,
                                    },
                                    {
                                        label: 'Late',
                                        data: charts.attendance.late,
                                    },
                                    {
                                        label: 'Absent',
                                        data: charts.attendance.absent,
                                    },
                                ]}
                            />
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Employees by department</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Chart
                                type="doughnut"
                                labels={charts.employees_by_department.labels}
                                datasets={[
                                    {
                                        label: 'Employees',
                                        data: charts.employees_by_department
                                            .data,
                                    },
                                ]}
                            />
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Leave request status</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Chart
                                type="bar"
                                labels={charts.leave_status.labels}
                                datasets={[
                                    {
                                        label: 'Requests',
                                        data: charts.leave_status.data,
                                    },
                                ]}
                            />
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Payroll totals (last 6 months)</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Chart
                                type="bar"
                                labels={charts.payroll_trend.labels}
                                datasets={[
                                    {
                                        label: 'Net salary',
                                        data: charts.payroll_trend.data,
                                    },
                                ]}
                            />
                        </CardContent>
                    </Card>
                </div>

                <Card>
                    <CardHeader className="flex flex-row items-center justify-between">
                        <CardTitle className="flex items-center gap-2">
                            <BriefcaseBusiness className="size-4" /> Recent
                            projects
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-sm">
                                <thead className="text-muted-foreground">
                                    <tr>
                                        <th className="py-2 font-medium">
                                            Project
                                        </th>
                                        <th className="py-2 font-medium">
                                            Client
                                        </th>
                                        <th className="py-2 font-medium">
                                            Progress
                                        </th>
                                        <th className="py-2 font-medium">
                                            Start date
                                        </th>
                                        <th className="py-2 font-medium">
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {recent_projects.map((project) => (
                                        <tr key={project.id} className="border-t">
                                            <td className="py-3 font-medium">
                                                {project.title}
                                            </td>
                                            <td className="py-3">
                                                {project.client}
                                            </td>
                                            <td className="py-3">
                                                <div className="flex items-center gap-2">
                                                    <div className="bg-muted h-2 w-24 overflow-hidden rounded-full">
                                                        <div
                                                            className="h-full rounded-full bg-primary"
                                                            style={{
                                                                width: `${project.progress}%`,
                                                            }}
                                                        />
                                                    </div>
                                                    <span className="text-muted-foreground text-xs">
                                                        {project.progress}%
                                                    </span>
                                                </div>
                                            </td>
                                            <td className="text-muted-foreground py-3">
                                                {project.start_date}
                                            </td>
                                            <td className="py-3">
                                                <StatusBadge
                                                    status={project.status}
                                                />
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [{ title: 'Dashboard', href: dashboard() }],
};
