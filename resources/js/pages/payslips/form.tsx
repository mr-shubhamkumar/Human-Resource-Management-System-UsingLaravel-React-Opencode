import { Form, Head, Link } from '@inertiajs/react';
import PayslipController from '@/actions/App/Http/Controllers/HRM/PayslipController';
import FormField from '@/components/hrm/form-field';
import PageHeader from '@/components/hrm/page-header';
import { SelectInput } from '@/components/hrm/form-controls';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { index } from '@/routes/payslips';

type Payslip = {
    id: number;
    payroll_id: number;
    generated_at: string;
    file_path: string;
};

type Props = {
    payslip: Payslip | null;
    payrolls: Array<{ id: number; label: string }>;
    defaultFilePath: string;
};

export default function PayslipForm({
    payslip,
    payrolls,
    defaultFilePath,
}: Props) {
    return (
        <>
            <Head title={payslip ? 'Edit payslip' : 'Add payslip'} />
            <div className="mx-auto max-w-2xl space-y-6 p-4">
                <PageHeader
                    title={payslip ? 'Edit payslip' : 'Add payslip'}
                    description="Link a payslip document to a payroll record."
                    action={
                        <Button asChild variant="outline">
                            <Link href={index()}>Back to list</Link>
                        </Button>
                    }
                />

                <Form
                    {...(payslip
                        ? PayslipController.update.form(payslip.id)
                        : PayslipController.store.form())}
                    options={{ preserveScroll: true }}
                    className="bg-card space-y-6 rounded-xl border p-6 shadow-sm"
                >
                    {({ errors, processing }) => (
                        <>
                            <FormField
                                label="Payroll"
                                name="payroll_id"
                                error={errors.payroll_id}
                            >
                                <SelectInput
                                    id="payroll_id"
                                    name="payroll_id"
                                    defaultValue={payslip?.payroll_id ?? ''}
                                    required
                                >
                                    <option value="">
                                        Select a payroll record
                                    </option>
                                    {payrolls.map((payroll) => (
                                        <option
                                            key={payroll.id}
                                            value={payroll.id}
                                        >
                                            {payroll.label}
                                        </option>
                                    ))}
                                </SelectInput>
                            </FormField>

                            <FormField
                                label="Generated at"
                                name="generated_at"
                                error={errors.generated_at}
                            >
                                <Input
                                    id="generated_at"
                                    name="generated_at"
                                    type="datetime-local"
                                    defaultValue={payslip?.generated_at ?? ''}
                                />
                            </FormField>

                            <FormField
                                label="File path"
                                name="file_path"
                                error={errors.file_path}
                                hint="Dummy PDF path used for demo purposes."
                            >
                                <Input
                                    id="file_path"
                                    name="file_path"
                                    defaultValue={
                                        payslip?.file_path ?? defaultFilePath
                                    }
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
                                    {payslip ? 'Save changes' : 'Create payslip'}
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}

PayslipForm.layout = {
    breadcrumbs: [{ title: 'Payslips', href: index() }],
};
