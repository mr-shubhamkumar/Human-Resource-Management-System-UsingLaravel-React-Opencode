import { Form, Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import EmployeeController from '@/actions/App/Http/Controllers/HRM/EmployeeController';
import FormField from '@/components/hrm/form-field';
import PageHeader from '@/components/hrm/page-header';
import { SelectInput, TextareaInput } from '@/components/hrm/form-controls';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Input } from '@/components/ui/input';
import { index as employeesIndex } from '@/routes/employees';

type Employee = {
    id: number;
    employee_id: string;
    first_name: string;
    last_name: string;
    email: string;
    phone: string | null;
    gender: string | null;
    date_of_birth: string | null;
    address: string | null;
    hire_date: string;
    department_id: number;
    position_id: number;
    status: string;
    is_approved: boolean;
    profile_photo: string | null;
    profile_photo_url: string | null;
};

type Option = { id: number; name?: string; title?: string };

type Props = {
    employee: Employee | null;
    departments: Array<{ id: number; name: string }>;
    positions: Array<{ id: number; department_id: number; title: string }>;
    nextEmployeeId: string;
};

function dateOnly(value?: string | null): string {
    return value ? value.slice(0, 10) : '';
}

export default function EmployeeForm({
    employee,
    departments,
    positions,
    nextEmployeeId,
}: Props) {
    const [departmentId, setDepartmentId] = useState<number | string>(
        employee?.department_id ?? departments[0]?.id ?? '',
    );
    const availablePositions = positions.filter(
        (position) => position.department_id === Number(departmentId),
    );

    return (
        <>
            <Head title={employee ? 'Edit employee' : 'Add employee'} />
            <div className="mx-auto max-w-4xl space-y-6 p-4">
                <PageHeader
                    title={employee ? 'Edit employee' : 'Add employee'}
                    description="Employee details and approval status."
                    action={
                        <Button asChild variant="outline">
                            <Link href={employeesIndex()}>Back to list</Link>
                        </Button>
                    }
                />

                <Form
                    {...(employee
                        ? EmployeeController.update.form(employee.id)
                        : EmployeeController.store.form())}
                    options={{ preserveScroll: true }}
                    encType="multipart/form-data"
                    className="bg-card space-y-6 rounded-xl border p-6 shadow-sm"
                >
                    {({ errors, processing }) => (
                        <>
                            <div className="grid gap-6 md:grid-cols-2">
                                <FormField
                                    label="Employee ID"
                                    name="employee_id"
                                    error={errors.employee_id}
                                >
                                    <Input
                                        id="employee_id"
                                        name="employee_id"
                                        defaultValue={
                                            employee?.employee_id ??
                                            nextEmployeeId
                                        }
                                        required
                                    />
                                </FormField>

                                <FormField
                                    label="Email address"
                                    name="email"
                                    error={errors.email}
                                >
                                    <Input
                                        id="email"
                                        name="email"
                                        type="email"
                                        defaultValue={employee?.email ?? ''}
                                        required
                                    />
                                </FormField>

                                <FormField
                                    label="First name"
                                    name="first_name"
                                    error={errors.first_name}
                                >
                                    <Input
                                        id="first_name"
                                        name="first_name"
                                        defaultValue={employee?.first_name ?? ''}
                                        required
                                    />
                                </FormField>

                                <FormField
                                    label="Last name"
                                    name="last_name"
                                    error={errors.last_name}
                                >
                                    <Input
                                        id="last_name"
                                        name="last_name"
                                        defaultValue={employee?.last_name ?? ''}
                                        required
                                    />
                                </FormField>

                                <FormField
                                    label="Phone"
                                    name="phone"
                                    error={errors.phone}
                                >
                                    <Input
                                        id="phone"
                                        name="phone"
                                        defaultValue={employee?.phone ?? ''}
                                        placeholder="09XXXXXXXXX"
                                    />
                                </FormField>

                                <FormField
                                    label="Gender"
                                    name="gender"
                                    error={errors.gender}
                                >
                                    <SelectInput
                                        id="gender"
                                        name="gender"
                                        defaultValue={employee?.gender ?? ''}
                                    >
                                        <option value="">Select gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </SelectInput>
                                </FormField>

                                <FormField
                                    label="Date of birth"
                                    name="date_of_birth"
                                    error={errors.date_of_birth}
                                >
                                    <Input
                                        id="date_of_birth"
                                        name="date_of_birth"
                                        type="date"
                                        defaultValue={dateOnly(
                                            employee?.date_of_birth,
                                        )}
                                    />
                                </FormField>

                                <FormField
                                    label="Hire date"
                                    name="hire_date"
                                    error={errors.hire_date}
                                >
                                    <Input
                                        id="hire_date"
                                        name="hire_date"
                                        type="date"
                                        defaultValue={dateOnly(
                                            employee?.hire_date,
                                        )}
                                        required
                                    />
                                </FormField>

                                <FormField
                                    label="Department"
                                    name="department_id"
                                    error={errors.department_id}
                                >
                                    <SelectInput
                                        id="department_id"
                                        name="department_id"
                                        value={departmentId}
                                        onChange={(event) =>
                                            setDepartmentId(event.target.value)
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
                                    label="Position"
                                    name="position_id"
                                    error={errors.position_id}
                                >
                                    <SelectInput
                                        id="position_id"
                                        name="position_id"
                                        defaultValue={employee?.position_id ?? ''}
                                        required
                                    >
                                        <option value="">Select position</option>
                                        {availablePositions.map((position) => (
                                            <option
                                                key={position.id}
                                                value={position.id}
                                            >
                                                {position.title}
                                            </option>
                                        ))}
                                    </SelectInput>
                                </FormField>

                                <FormField
                                    label="Status"
                                    name="status"
                                    error={errors.status}
                                >
                                    <SelectInput
                                        id="status"
                                        name="status"
                                        defaultValue={employee?.status ?? 'active'}
                                    >
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="terminated">
                                            Terminated
                                        </option>
                                    </SelectInput>
                                </FormField>

                                <FormField
                                    label="Profile photo"
                                    name="profile_photo"
                                    error={errors.profile_photo}
                                >
                                    <div className="flex items-center gap-3">
                                        <Avatar className="size-12">
                                            {employee?.profile_photo_url && (
                                                <AvatarImage
                                                    src={
                                                        employee.profile_photo_url
                                                    }
                                                    alt="Current profile photo"
                                                />
                                            )}
                                            <AvatarFallback>
                                                {employee
                                                    ? `${employee.first_name.charAt(0)}${employee.last_name.charAt(0)}`
                                                    : 'NEW'}
                                            </AvatarFallback>
                                        </Avatar>
                                        <Input
                                            id="profile_photo"
                                            name="profile_photo"
                                            type="file"
                                            accept="image/jpeg,image/png,image/webp"
                                            className="pt-2"
                                        />
                                    </div>
                                    <p className="text-muted-foreground mt-1 text-xs">
                                        JPG, PNG or WebP. Maximum 2 MB. Leave
                                        empty to keep the current photo.
                                    </p>
                                </FormField>
                            </div>

                            <FormField
                                label="Address"
                                name="address"
                                error={errors.address}
                            >
                                <TextareaInput
                                    id="address"
                                    name="address"
                                    defaultValue={employee?.address ?? ''}
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
                                        employee?.is_approved ?? true
                                    }
                                />
                                <span className="text-sm">
                                    Auto-approve this employee record
                                </span>
                            </label>

                            <div className="flex justify-end gap-2">
                                <Button asChild type="button" variant="outline">
                                    <Link href={employeesIndex()}>Cancel</Link>
                                </Button>
                                <Button type="submit" disabled={processing}>
                                    {employee ? 'Save changes' : 'Create employee'}
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}

EmployeeForm.layout = {
    breadcrumbs: [{ title: 'Employees', href: employeesIndex() }],
};
