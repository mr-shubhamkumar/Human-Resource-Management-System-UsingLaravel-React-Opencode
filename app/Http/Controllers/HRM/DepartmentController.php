<?php

namespace App\Http\Controllers\HRM;

use App\Http\Controllers\Controller;
use App\Http\Requests\HRM\DepartmentRequest;
use App\Models\Department;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DepartmentController extends Controller
{
    /**
     * Display a paginated, searchable list of departments.
     */
    public function index(Request $request): Response
    {
        $search = trim((string) $request->input('search', ''));

        $departments = Department::query()
            ->withCount('employees')
            ->when($search !== '', fn (Builder $query) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('departments/index', [
            'departments' => $departments,
            'filters' => ['search' => $search],
        ]);
    }

    /**
     * Show the form for creating a new department.
     */
    public function create(): Response
    {
        return Inertia::render('departments/form', ['department' => null]);
    }

    /**
     * Store a newly created department.
     */
    public function store(DepartmentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_approved'] = $request->boolean('is_approved');

        Department::create($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Department created successfully.']);

        return to_route('departments.index');
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(Department $department): Response
    {
        return Inertia::render('departments/form', ['department' => $department]);
    }

    /**
     * Update the specified department.
     */
    public function update(DepartmentRequest $request, Department $department): RedirectResponse
    {
        $data = $request->validated();
        $data['is_approved'] = $request->boolean('is_approved');

        $department->update($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Department updated successfully.']);

        return to_route('departments.index');
    }

    /**
     * Remove the specified department.
     */
    public function destroy(Department $department): RedirectResponse
    {
        $department->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Department deleted successfully.']);

        return to_route('departments.index');
    }
}
