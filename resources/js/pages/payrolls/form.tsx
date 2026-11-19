import { Form, Head, Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import PayrollController from '@/actions/App/Http/Controllers/HRM/PayrollController';
import FormField from '@/components/hrm/form-field';
import PageHeader from '@/components/hrm/page-header';
import { SelectInput } from '@/components/hrm/form-controls';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { index } from '@/routes/payrolls';

type Payroll = {
    id: number;
    employee_id: number;
    basic_salary: string;
    bonus: string;
    deductions: string;
    net_salary: string;
    pay_date: string;
};

type Props = {
    payroll: Payroll | null;
    employees: Array<{ id: number; label: string }>;
};

export default function PayrollForm({ payroll, employees }: Props) {
    const [basicSalary, setBasicSalary] = useState(
        payroll?.basic_salary ?? '0',
    );
    const [bonus, setBonus] = useState(payroll?.bonus ?? '0');
    const [deductions, setDeductions] = useState(payroll?.deductions ?? '0');
    const netSalary = (
        Number(basicSalary) +
        Number(bonus) -
        Number(deductions)
    ).toFixed(2);

    useEffect(() => {
        if (!payroll) {
            setBasicSalary('0');
            setBonus('0');
            setDeductions('0');
        }
    }, [payroll]);

    return (
        <>
            <Head title={payroll ? 'Edit payroll' : 'Add payroll'} />
            <div className="mx-auto max-w-3xl space-y-6 p-4">
                <PageHeader
                    title={payroll ? 'Edit payroll' : 'Add payroll'}
                    description="Net salary is calculated automatically."
                    action={
                        <Button asChild variant="outline">
                            <Link href={index()}>Back to list</Link>
                        </Button>
                    }
                />

                <Form
                    {...(payroll
                        ? PayrollController.update.form(payroll.id)
                        : PayrollController.store.form())}
                    options={{ preserveScroll: true }}
                    className="bg-card space-y-6 rounded-xl border p-6 shadow-sm"
                >
                    {({ errors, processing }) => (
                        <>
                            <div className="grid gap-6 sm:grid-cols-2">
                                <FormField
                                    label="Employee"
                                    name="employee_id"
                                    error={errors.employee_id}
                                >
                                    <SelectInput
                                        id="employee_id"
                                        name="employee_id"
                                        defaultValue={payroll?.employee_id ?? ''}
                                        required
                                    >
                                        <option value="">
                                            Select an employee
                                        </option>
                                        {employees.map((employee) => (
                                            <option
                                                key={employee.id}
                                                value={employee.id}
                                            >
                                                {employee.label}
                                            </option>
                                        ))}
                                    </SelectInput>
                                </FormField>

                                <FormField
                                    label="Pay date"
                                    name="pay_date"
                                    error={errors.pay_date}
                                >
                                    <Input
                                        id="pay_date"
                                        name="pay_date"
                                        type="date"
                                        defaultValue={
                                            payroll?.pay_date?.slice(0, 10) ?? ''
                                        }
                                        required
                                    />
                                </FormField>

                                <FormField
                                    label="Basic salary"
                                    name="basic_salary"
                                    error={errors.basic_salary}
                                >
                                    <Input
                                        id="basic_salary"
                                        name="basic_salary"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        value={basicSalary}
                                        onChange={(event) =>
                                            setBasicSalary(event.target.value)
                                        }
                                        required
                                    />
                                </FormField>

                                <FormField
                                    label="Bonus"
                                    name="bonus"
                                    error={errors.bonus}
                                >
                                    <Input
                                        id="bonus"
                                        name="bonus"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        value={bonus}
                                        onChange={(event) =>
                                            setBonus(event.target.value)
                                        }
                                    />
                                </FormField>

                                <FormField
                                    label="Deductions"
                                    name="deductions"
                                    error={errors.deductions}
                                >
                                    <Input
                                        id="deductions"
                                        name="deductions"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        value={deductions}
                                        onChange={(event) =>
                                            setDeductions(event.target.value)
                                        }
                                    />
                                </FormField>

                                <FormField label="Net salary" name="net_salary">
                                    <div className="border-input flex h-9 items-center rounded-md border bg-muted/40 px-3 text-sm font-medium">
                                        {Number(netSalary).toLocaleString(
                                            undefined,
                                            {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2,
                                            },
                                        )}
                                    </div>
                                </FormField>
                            </div>

                            <div className="flex justify-end gap-2">
                                <Button
                                    asChild
                                    type="button"
                                    variant="outline"
                                >
                                    <Link href={index()}>Cancel</Link>
                                </Button>
                                <Button type="submit" disabled={processing}>
                                    {payroll
                                        ? 'Save changes'
                                        : 'Create payroll'}
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}

PayrollForm.layout = {
    breadcrumbs: [{ title: 'Payrolls', href: index() }],
};
