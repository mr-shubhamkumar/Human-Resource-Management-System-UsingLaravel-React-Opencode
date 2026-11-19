import { Head, Link } from '@inertiajs/react';
import { Pencil } from 'lucide-react';
import PayslipController from '@/actions/App/Http/Controllers/HRM/PayslipController';
import DataTable, { type Paginator } from '@/components/hrm/data-table';
import DeleteAction from '@/components/hrm/delete-action';
import PageHeader from '@/components/hrm/page-header';
import { Button } from '@/components/ui/button';
import { create, edit, index } from '@/routes/payslips';

type Payslip = {
    id: number;
    file_path: string;
    generated_at: string;
    payroll: {
        pay_date: string;
        net_salary: string;
        employee: {
            first_name: string;
            last_name: string;
            employee_id: string;
        } | null;
    } | null;
};

type Props = {
    payslips: Paginator<Payslip>;
    filters: { search: string };
};

export default function PayslipsIndex({ payslips, filters }: Props) {
    return (
        <>
            <Head title="Payslips" />
            <div className="space-y-6 p-4">
                <PageHeader
                    title="Payslips"
                    description="Generated payslip documents for each payroll."
                    action={
                        <Button asChild>
                            <Link href={create()}>Add payslip</Link>
                        </Button>
                    }
                />

                <DataTable
                    columns={[
                        {
                            key: 'payroll',
                            label: 'Employee',
                            render: (payslip) => {
                                const employee = payslip.payroll?.employee;

                                return employee
                                    ? `${employee.first_name} ${employee.last_name} (${employee.employee_id})`
                                    : '—';
                            },
                        },
                        {
                            key: 'pay_date',
                            label: 'Pay period',
                            render: (payslip) =>
                                payslip.payroll?.pay_date?.slice(0, 10) ?? '—',
                        },
                        {
                            key: 'net_salary',
                            label: 'Net salary',
                            className: 'text-right',
                            render: (payslip) =>
                                payslip.payroll
                                    ? Number(
                                          payslip.payroll.net_salary,
                                      ).toLocaleString(undefined, {
                                          minimumFractionDigits: 2,
                                          maximumFractionDigits: 2,
                                      })
                                    : '—',
                        },
                        {
                            key: 'file_path',
                            label: 'File path',
                            render: (payslip) => (
                                <span className="text-muted-foreground text-xs">
                                    {payslip.file_path}
                                </span>
                            ),
                        },
                        {
                            key: 'generated_at',
                            label: 'Generated',
                            render: (payslip) =>
                                payslip.generated_at.slice(0, 10),
                        },
                        {
                            key: 'actions',
                            label: 'Actions',
                            className: 'text-right',
                            render: (payslip) => (
                                <div className="flex justify-end gap-1">
                                    <Button asChild variant="ghost" size="icon">
                                        <Link
                                            href={edit(payslip.id)}
                                            aria-label="Edit payslip"
                                        >
                                            <Pencil className="size-4" />
                                        </Link>
                                    </Button>
                                    <DeleteAction
                                        action={PayslipController.destroy.form(
                                            payslip.id,
                                        )}
                                        title="Delete payslip"
                                    />
                                </div>
                            ),
                        },
                    ]}
                    rows={payslips.data}
                    rowKey={(payslip) => payslip.id}
                    pagination={payslips}
                    search={filters.search}
                    searchRoute={index().url}
                    emptyMessage="No payslips found."
                />
            </div>
        </>
    );
}

PayslipsIndex.layout = {
    breadcrumbs: [{ title: 'Payslips', href: index() }],
};
