<?php

namespace App\Http\Controllers\HRM;

use App\Http\Controllers\Controller;
use App\Http\Requests\HRM\EmployeeRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeController extends Controller
{
    /**
     * Display a paginated, searchable list of employees.
     */
    public function index(Request $request): Response
    {
        $search = trim((string) $request->input('search', ''));

        $employees = Employee::query()
            ->with(['department:id,name', 'position:id,title'])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where('employee_id', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('department', fn (Builder $query) => $query->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('position', fn (Builder $query) => $query->where('title', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('employees/index', [
            'employees' => $employees,
            'filters' => ['search' => $search],
            'nextEmployeeId' => sprintf('EMP%04d', Employee::count() + 1),
        ]);
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create(): Response
    {
        return Inertia::render('employees/form', [
            'employee' => null,
            'departments' => Department::query()->orderBy('name')->get(['id', 'name']),
            'positions' => Position::query()->orderBy('title')->get(['id', 'department_id', 'title']),
            'nextEmployeeId' => sprintf('EMP%04d', Employee::count() + 1),
        ]);
    }

    /**
     * Store a newly created employee.
     */
    public function store(EmployeeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_approved'] = $request->boolean('is_approved');

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')->store('employees/profile-photos', 'public');
        }

        Employee::create($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Employee created successfully.']);

        return to_route('employees.index');
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee): Response
    {
        return Inertia::render('employees/form', [
            'employee' => $employee->loadMissing(['department:id,name', 'position:id,title']),
            'departments' => Department::query()->orderBy('name')->get(['id', 'name']),
            'positions' => Position::query()->orderBy('title')->get(['id', 'department_id', 'title']),
            'nextEmployeeId' => $employee->employee_id,
        ]);
    }

    /**
     * Update the specified employee.
     */
    public function update(EmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $data = $request->validated();
        $data['is_approved'] = $request->boolean('is_approved');
        $previousPhoto = $employee->profile_photo;

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')->store('employees/profile-photos', 'public');
        } else {
            unset($data['profile_photo']);
        }

        $employee->update($data);

        if ($request->hasFile('profile_photo') && $previousPhoto) {
            Storage::disk('public')->delete($previousPhoto);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Employee updated successfully.']);

        return to_route('employees.index');
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        $photo = $employee->profile_photo;

        $employee->delete();

        if ($photo) {
            Storage::disk('public')->delete($photo);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Employee deleted successfully.']);

        return to_route('employees.index');
    }
}
