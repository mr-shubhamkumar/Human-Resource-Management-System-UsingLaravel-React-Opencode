import { Form, Head, Link } from '@inertiajs/react';
import PositionController from '@/actions/App/Http/Controllers/HRM/PositionController';
import { SelectInput, TextareaInput } from '@/components/hrm/form-controls';
import FormField from '@/components/hrm/form-field';
import PageHeader from '@/components/hrm/page-header';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { index } from '@/routes/positions';

type Position = {
    id: number;
    department_id: number;
    title: string;
    description: string | null;
    salary_min: string;
    salary_max: string;
};

type Props = {
    position: Position | null;
    departments: Array<{ id: number; name: string }>;
};

export default function PositionForm({ position, departments }: Props) {
    return (
        <>
            <Head title={position ? 'Edit position' : 'Add position'} />
            <div className="mx-auto max-w-2xl space-y-6 p-4">
                <PageHeader
                    title={position ? 'Edit position' : 'Add position'}
                    description="Define a job title and its salary band."
                    action={
                        <Button asChild variant="outline">
                            <Link href={index()}>Back to list</Link>
                        </Button>
                    }
                />

                <Form
                    {...(position
                        ? PositionController.update.form(position.id)
                        : PositionController.store.form())}
                    options={{ preserveScroll: true }}
                    className="bg-card space-y-6 rounded-xl border p-6 shadow-sm"
                >
                    {({ errors, processing }) => (
                        <>
                            <FormField
                                label="Department"
                                name="department_id"
                                error={errors.department_id}
                            >
                                <SelectInput
                                    id="department_id"
                                    name="department_id"
                                    defaultValue={
                                        position?.department_id ??
                                        departments[0]?.id ??
                                        ''
                                    }
                                    required
                                >
                                    {departments.map((department) => (
                                        <option
                                            key={department.id}
                                            value={department.id}
                                        >
                                            {department.name}
                                        </option>
                                    ))}
                                </SelectInput>
                            </FormField>

                            <FormField
                                label="Title"
                                name="title"
                                error={errors.title}
                            >
                                <Input
                                    id="title"
                                    name="title"
                                    defaultValue={position?.title ?? ''}
                                    required
                                />
                            </FormField>

                            <div className="grid gap-6 sm:grid-cols-2">
                                <FormField
                                    label="Minimum salary"
                                    name="salary_min"
                                    error={errors.salary_min}
                                >
                                    <Input
                                        id="salary_min"
                                        name="salary_min"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        defaultValue={
                                            position?.salary_min ?? '25000'
                                        }
                                        required
                                    />
                                </FormField>

                                <FormField
                                    label="Maximum salary"
                                    name="salary_max"
                                    error={errors.salary_max}
                                >
                                    <Input
                                        id="salary_max"
                                        name="salary_max"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        defaultValue={
                                            position?.salary_max ?? '60000'
                                        }
                                        required
                                    />
                                </FormField>
                            </div>

                            <FormField
                                label="Description"
                                name="description"
                                error={errors.description}
                            >
                                <TextareaInput
                                    id="description"
                                    name="description"
                                    defaultValue={position?.description ?? ''}
                                />
                            </FormField>

                            <div className="flex justify-end gap-2">
                                <Button asChild type="button" variant="outline">
                                    <Link href={index()}>Cancel</Link>
                                </Button>
                                <Button type="submit" disabled={processing}>
                                    {position
                                        ? 'Save changes'
                                        : 'Create position'}
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}

PositionForm.layout = {
    breadcrumbs: [{ title: 'Positions', href: index() }],
};
