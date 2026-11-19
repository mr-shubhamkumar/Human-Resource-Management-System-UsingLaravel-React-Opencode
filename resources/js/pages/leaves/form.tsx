import { Form, Head, Link } from '@inertiajs/react';
import LeaveController from '@/actions/App/Http/Controllers/HRM/LeaveController';
import { SelectInput, TextareaInput } from '@/components/hrm/form-controls';
import FormField from '@/components/hrm/form-field';
import PageHeader from '@/components/hrm/page-header';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { index } from '@/routes/leaves';

type Leave = {
    id: number;
    employee_id: number;
    leave_type_id: number;
    start_date: string;
    end_date: string;
    reason: string;
    status: string;
    approved_by: number | null;
};

type Props = {
    leave: Leave | null;
    employees: Array<{ id: number; label: string }>;
    leaveTypes: Array<{ id: number; name: string; days_allowed: number }>;
    approvers: Array<{ id: number; name: string }>;
};

export default function LeaveForm({
    leave,
    employees,
    leaveTypes,
    approvers,
}: Props) {
    return (
        <>
            <Head title={leave ? 'Edit leave' : 'Add leave'} />
            <div className="mx-auto max-w-3xl space-y-6 p-4">
                <PageHeader
                    title={leave ? 'Edit leave request' : 'Add leave request'}
                    description="Create or approve an employee leave application."
                    action={
                        <Button asChild variant="outline">
                            <Link href={index()}>Back to list</Link>
                        </Button>
                    }
                />

                <Form
                    {...(leave
                        ? LeaveController.update.form(leave.id)
                        : LeaveController.store.form())}
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
                                        defaultValue={leave?.employee_id ?? ''}
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
                                    label="Leave type"
                                    name="leave_type_id"
                                    error={errors.leave_type_id}
                                >
                                    <SelectInput
                                        id="leave_type_id"
                                        name="leave_type_id"
                                        defaultValue={
                                            leave?.leave_type_id ?? ''
                                        }
                                        required
                                    >
                                        <option value="">
                                            Select a leave type
                                        </option>
                                        {leaveTypes.map((type) => (
                                            <option
                                                key={type.id}
                                                value={type.id}
                                            >
                                                {type.name} ({type.days_allowed}{' '}
                                                days)
                                            </option>
                                        ))}
                                    </SelectInput>
                                </FormField>

                                <FormField
                                    label="Start date"
                                    name="start_date"
                                    error={errors.start_date}
                                >
                                    <Input
                                        id="start_date"
                                        name="start_date"
                                        type="date"
                                        defaultValue={
                                            leave?.start_date?.slice(0, 10) ??
                                            ''
                                        }
                                        required
                                    />
                                </FormField>

                                <FormField
                                    label="End date"
                                    name="end_date"
                                    error={errors.end_date}
                                >
                                    <Input
                                        id="end_date"
                                        name="end_date"
                                        type="date"
                                        defaultValue={
                                            leave?.end_date?.slice(0, 10) ?? ''
                                        }
                                        required
                                    />
                                </FormField>

                                <FormField
                                    label="Status"
                                    name="status"
                                    error={errors.status}
                                >
                                    <SelectInput
                                        id="status"
                                        name="status"
                                        defaultValue={leave?.status ?? 'pending'}
                                    >
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </SelectInput>
                                </FormField>

                                <FormField
                                    label="Approved by"
                                    name="approved_by"
                                    error={errors.approved_by}
                                >
                                    <SelectInput
                                        id="approved_by"
                                        name="approved_by"
                                        defaultValue={leave?.approved_by ?? ''}
                                    >
                                        <option value="">No approver</option>
                                        {approvers.map((approver) => (
                                            <option
                                                key={approver.id}
                                                value={approver.id}
                                            >
                                                {approver.name}
                                            </option>
                                        ))}
                                    </SelectInput>
                                </FormField>
                            </div>

                            <FormField
                                label="Reason"
                                name="reason"
                                error={errors.reason}
                            >
                                <TextareaInput
                                    id="reason"
                                    name="reason"
                                    defaultValue={leave?.reason ?? ''}
                                    required
                                />
                            </FormField>

                            <div className="flex justify-end gap-2">
                                <Button
                                    asChild
                                    type="button"
                                    variant="outline"
                                >
                                    <Link href={index()}>Cancel</Link>
                                </Button>
                                <Button type="submit" disabled={processing}>
                                    {leave ? 'Save changes' : 'Create leave'}
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}

LeaveForm.layout = {
    breadcrumbs: [{ title: 'Leaves', href: index() }],
};
