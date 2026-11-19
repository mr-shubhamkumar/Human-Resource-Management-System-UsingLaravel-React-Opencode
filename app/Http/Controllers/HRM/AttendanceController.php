<?php

namespace App\Http\Controllers\HRM;

use App\Http\Controllers\Controller;
use App\Http\Requests\HRM\AttendanceRequest;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceController extends Controller
{
    /**
     * Display a paginated, searchable list of attendance records.
     */
    public function index(Request $request): Response
    {
        $search = trim((string) $request->input('search', ''));

        $attendances = Attendance::query()
            ->with('employee:id,employee_id,first_name,last_name')
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where('date', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('employee', function (Builder $query) use ($search): void {
                        $query->where('employee_id', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            })
            ->latest('date')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('attendances/index', [
            'attendances' => $attendances,
            'filters' => ['search' => $search],
        ]);
    }

    /**
     * Show the form for creating a new attendance record.
     */
    public function create(): Response
    {
        return Inertia::render('attendances/form', [
            'attendance' => null,
            'employees' => $this->employeeOptions(),
        ]);
    }

    /**
     * Store a newly created attendance record.
     */
    public function store(AttendanceRequest $request): RedirectResponse
    {
        Attendance::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Attendance recorded successfully.']);

        return to_route('attendances.index');
    }

    /**
     * Show the form for editing the specified attendance record.
     */
    public function edit(Attendance $attendance): Response
    {
        return Inertia::render('attendances/form', [
            'attendance' => [
                ...$attendance->toArray(),
                'date' => $attendance->date->format('Y-m-d'),
            ],
            'employees' => $this->employeeOptions(),
        ]);
    }

    /**
     * Update the specified attendance record.
     */
    public function update(AttendanceRequest $request, Attendance $attendance): RedirectResponse
    {
        $attendance->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Attendance updated successfully.']);

        return to_route('attendances.index');
    }

    /**
     * Remove the specified attendance record.
     */
    public function destroy(Attendance $attendance): RedirectResponse
    {
        $attendance->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Attendance deleted successfully.']);

        return to_route('attendances.index');
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
