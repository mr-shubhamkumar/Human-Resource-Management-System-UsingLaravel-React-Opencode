import { Form, Head, Link } from '@inertiajs/react';
import AttendanceController from '@/actions/App/Http/Controllers/HRM/AttendanceController';
import { SelectInput } from '@/components/hrm/form-controls';
import FormField from '@/components/hrm/form-field';
import PageHeader from '@/components/hrm/page-header';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { index } from '@/routes/attendances';

type Attendance = {
    id: number;
    employee_id: number;
    date: string;
    check_in: string | null;
    check_out: string | null;
    status: string;
};

type Props = {
    attendance: Attendance | null;
    employees: Array<{ id: number; label: string }>;
};

function timeOnly(value?: string | null): string {
    return value ? value.slice(0, 5) : '';
}

export default function AttendanceForm({ attendance, employees }: Props) {
    return (
        <>
            <Head title={attendance ? 'Edit attendance' : 'Add attendance'} />
            <div className="mx-auto max-w-2xl space-y-6 p-4">
                <PageHeader
                    title={attendance ? 'Edit attendance' : 'Add attendance'}
                    description="Record an employee's daily attendance."
                    action={
                        <Button asChild variant="outline">
                            <Link href={index()}>Back to list</Link>
                        </Button>
                    }
                />

                <Form
                    {...(attendance
                        ? AttendanceController.update.form(attendance.id)
                        : AttendanceController.store.form())}
                    options={{ preserveScroll: true }}
                    className="bg-card space-y-6 rounded-xl border p-6 shadow-sm"
                >
                    {({ errors, processing }) => (
                        <>
                            <FormField
                                label="Employee"
                                name="employee_id"
                                error={errors.employee_id}
                            >
                                <SelectInput
                                    id="employee_id"
                                    name="employee_id"
                                    defaultValue={attendance?.employee_id ?? ''}
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

                            <div className="grid gap-6 sm:grid-cols-3">
                                <FormField
                                    label="Date"
                                    name="date"
                                    error={errors.date}
                                >
                                    <Input
                                        id="date"
                                        name="date"
                                        type="date"
                                        defaultValue={
                                            attendance?.date?.slice(0, 10) ?? ''
                                        }
                                        required
                                    />
                                </FormField>

                                <FormField
                                    label="Check in"
                                    name="check_in"
                                    error={errors.check_in}
                                >
                                    <Input
                                        id="check_in"
                                        name="check_in"
                                        type="time"
                                        defaultValue={timeOnly(
                                            attendance?.check_in,
                                        )}
                                    />
                                </FormField>

                                <FormField
                                    label="Check out"
                                    name="check_out"
                                    error={errors.check_out}
                                >
                                    <Input
                                        id="check_out"
                                        name="check_out"
                                        type="time"
                                        defaultValue={timeOnly(
                                            attendance?.check_out,
                                        )}
                                    />
                                </FormField>
                            </div>

                            <FormField
                                label="Status"
                                name="status"
                                error={errors.status}
                            >
                                <SelectInput
                                    id="status"
                                    name="status"
                                    defaultValue={attendance?.status ?? 'present'}
                                >
                                    <option value="present">Present</option>
                                    <option value="late">Late</option>
                                    <option value="absent">Absent</option>
                                </SelectInput>
                            </FormField>

                            <div className="flex justify-end gap-2">
                                <Button asChild type="button" variant="outline">
                                    <Link href={index()}>Cancel</Link>
                                </Button>
                                <Button type="submit" disabled={processing}>
                                    {attendance
                                        ? 'Save changes'
                                        : 'Create attendance'}
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}

AttendanceForm.layout = {
    breadcrumbs: [{ title: 'Attendance', href: index() }],
};
