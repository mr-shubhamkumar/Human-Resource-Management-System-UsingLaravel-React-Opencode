import { Form, Head, Link } from '@inertiajs/react';
import DepartmentController from '@/actions/App/Http/Controllers/HRM/DepartmentController';
import { TextareaInput } from '@/components/hrm/form-controls';
import FormField from '@/components/hrm/form-field';
import PageHeader from '@/components/hrm/page-header';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { index } from '@/routes/departments';

type Department = {
    id: number;
    name: string;
    description: string | null;
    is_approved: boolean;
};

export default function DepartmentForm({
    department,
}: {
    department: Department | null;
}) {
    return (
        <>
            <Head title={department ? 'Edit department' : 'Add department'} />
            <div className="mx-auto max-w-2xl space-y-6 p-4">
                <PageHeader
                    title={department ? 'Edit department' : 'Add department'}
                    description="Set up a team and approve it for use."
                    action={
                        <Button asChild variant="outline">
                            <Link href={index()}>Back to list</Link>
                        </Button>
                    }
                />

                <Form
                    {...(department
                        ? DepartmentController.update.form(department.id)
                        : DepartmentController.store.form())}
                    options={{ preserveScroll: true }}
                    className="bg-card space-y-6 rounded-xl border p-6 shadow-sm"
                >
                    {({ errors, processing }) => (
                        <>
                            <FormField
                                label="Department name"
                                name="name"
                                error={errors.name}
                            >
                                <Input
                                    id="name"
                                    name="name"
                                    defaultValue={department?.name ?? ''}
                                    required
                                />
                            </FormField>

                            <FormField
                                label="Description"
                                name="description"
                                error={errors.description}
                            >
                                <TextareaInput
                                    id="description"
                                    name="description"
                                    defaultValue={department?.description ?? ''}
                                />
                            </FormField>

                            <label
                                htmlFor="is_approved"
                                className="flex items-center gap-3"
                            >
                                <Checkbox
                                    id="is_approved"
                                    name="is_approved"
                                    value="1"
                                    defaultChecked={
                                        department?.is_approved ?? true
                                    }
                                />
                                <span className="text-sm">
                                    Auto-approve this department
                                </span>
                            </label>

                            <div className="flex justify-end gap-2">
                                <Button asChild type="button" variant="outline">
                                    <Link href={index()}>Cancel</Link>
                                </Button>
                                <Button type="submit" disabled={processing}>
                                    {department
                                        ? 'Save changes'
                                        : 'Create department'}
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}

DepartmentForm.layout = {
    breadcrumbs: [{ title: 'Departments', href: index() }],
};
