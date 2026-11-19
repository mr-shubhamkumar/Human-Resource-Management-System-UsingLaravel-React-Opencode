import { Head, Link } from '@inertiajs/react';
import { Pencil } from 'lucide-react';
import PayrollController from '@/actions/App/Http/Controllers/HRM/PayrollController';
import DataTable, { type Paginator } from '@/components/hrm/data-table';
import DeleteAction from '@/components/hrm/delete-action';
import PageHeader from '@/components/hrm/page-header';
import { Button } from '@/components/ui/button';
import { create, edit, index } from '@/routes/payrolls';

type Payroll = {
    id: number;
    pay_date: string;
    basic_salary: string;
    bonus: string;
    deductions: string;
    net_salary: string;
    employee: {
        first_name: string;
        last_name: string;
        employee_id: string;
    } | null;
};

type Props = {
    payrolls: Paginator<Payroll>;
    filters: { search: string };
};

function money(value: string): string {
    return Number(value).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

export default function PayrollsIndex({ payrolls, filters }: Props) {
    return (
        <>
            <Head title="Payrolls" />
            <div className="space-y-6 p-4">
                <PageHeader
                    title="Payrolls"
                    description="Monthly salary computations for all employees."
                    action={
                        <Button asChild>
                            <Link href={create()}>Add payroll</Link>
                        </Button>
                    }
                />

                <DataTable
                    columns={[
                        {
                            key: 'pay_date',
                            label: 'Pay date',
                            render: (payroll) =>
                                payroll.pay_date.slice(0, 10),
                        },
                        {
                            key: 'employee',
                            label: 'Employee',
                            render: (payroll) =>
                                payroll.employee
                                    ? `${payroll.employee.first_name} ${payroll.employee.last_name} (${payroll.employee.employee_id})`
                                    : '—',
                        },
                        {
                            key: 'basic_salary',
                            label: 'Basic',
                            className: 'text-right',
                            render: (payroll) => money(payroll.basic_salary),
                        },
                        {
                            key: 'bonus',
                            label: 'Bonus',
                            className: 'text-right',
                            render: (payroll) => money(payroll.bonus),
                        },
                        {
                            key: 'deductions',
                            label: 'Deductions',
                            className: 'text-right',
                            render: (payroll) => money(payroll.deductions),
                        },
                        {
                            key: 'net_salary',
                            label: 'Net salary',
                            className: 'text-right font-medium',
                            render: (payroll) => money(payroll.net_salary),
                        },
                        {
                            key: 'actions',
                            label: 'Actions',
                            className: 'text-right',
                            render: (payroll) => (
                                <div className="flex justify-end gap-1">
                                    <Button asChild variant="ghost" size="icon">
                                        <Link
                                            href={edit(payroll.id)}
                                            aria-label="Edit payroll"
                                        >
                                            <Pencil className="size-4" />
                                        </Link>
                                    </Button>
                                    <DeleteAction
                                        action={PayrollController.destroy.form(
                                            payroll.id,
                                        )}
                                        title="Delete payroll"
                                        description="Deleting this payroll will also remove its payslips."
                                    />
                                </div>
                            ),
                        },
                    ]}
                    rows={payrolls.data}
                    rowKey={(payroll) => payroll.id}
                    pagination={payrolls}
                    search={filters.search}
                    searchRoute={index().url}
                    emptyMessage="No payroll records found."
                />
            </div>
        </>
    );
}

PayrollsIndex.layout = {
    breadcrumbs: [{ title: 'Payrolls', href: index() }],
};
