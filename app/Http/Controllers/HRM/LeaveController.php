<?php

namespace App\Http\Controllers\HRM;

use App\Http\Controllers\Controller;
use App\Http\Requests\HRM\LeaveRequest;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeaveController extends Controller
{
    /**
     * Display a paginated, searchable list of leave requests.
     */
    public function index(Request $request): Response
    {
        $search = trim((string) $request->input('search', ''));

        $leaves = Leave::query()
            ->with([
                'employee:id,employee_id,first_name,last_name',
                'leaveType:id,name',
                'approvedBy:id,name',
            ])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where('reason', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('employee', function (Builder $query) use ($search): void {
                        $query->where('employee_id', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('leaves/index', [
            'leaves' => $leaves,
            'filters' => ['search' => $search],
        ]);
    }

    /**
     * Show the form for creating a new leave request.
     */
    public function create(): Response
    {
        return Inertia::render('leaves/form', [
            'leave' => null,
            'employees' => $this->employeeOptions(),
            'leaveTypes' => LeaveType::query()->orderBy('name')->get(['id', 'name', 'days_allowed']),
            'approvers' => User::query()->whereNotNull('role_id')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created leave request.
     */
    public function store(LeaveRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($data['status'] !== 'pending' && empty($data['approved_by'])) {
            $data['approved_by'] = $request->user()?->id;
        }

        Leave::create($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Leave request created successfully.']);

        return to_route('leaves.index');
    }

    /**
     * Show the form for editing the specified leave request.
     */
    public function edit(Leave $leave): Response
    {
        return Inertia::render('leaves/form', [
            'leave' => [
                ...$leave->toArray(),
                'start_date' => $leave->start_date->format('Y-m-d'),
                'end_date' => $leave->end_date->format('Y-m-d'),
            ],
            'employees' => $this->employeeOptions(),
            'leaveTypes' => LeaveType::query()->orderBy('name')->get(['id', 'name', 'days_allowed']),
            'approvers' => User::query()->whereNotNull('role_id')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified leave request.
     */
    public function update(LeaveRequest $request, Leave $leave): RedirectResponse
    {
        $data = $request->validated();

        if ($data['status'] === 'pending') {
            $data['approved_by'] = null;
        } elseif (empty($data['approved_by'])) {
            $data['approved_by'] = $request->user()?->id;
        }

        $leave->update($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Leave request updated successfully.']);

        return to_route('leaves.index');
    }

    /**
     * Remove the specified leave request.
     */
    public function destroy(Leave $leave): RedirectResponse
    {
        $leave->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Leave request deleted successfully.']);

        return to_route('leaves.index');
    }

    /**
     * Get employees for the selection field.
     *
     * @return array<int, array<string, mixed>>
     */
    private function employeeOptions(): array
    {
        return Employee::query()
            ->orderBy('first_name')
            ->get(['id', 'employee_id', 'first_name', 'last_name'])
            ->map(fn (Employee $employee): array => [
                'id' => $employee->id,
                'label' => $employee->full_name.' ('.$employee->employee_id.')',
            ])
            ->all();
    }
}
