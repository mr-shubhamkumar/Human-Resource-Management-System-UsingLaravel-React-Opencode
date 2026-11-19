<?php

namespace App\Http\Controllers\HRM;

use App\Http\Controllers\Controller;
use App\Http\Requests\HRM\PayrollRequest;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PayrollController extends Controller
{
    /**
     * Display a paginated, searchable list of payroll records.
     */
    public function index(Request $request): Response
    {
        $search = trim((string) $request->input('search', ''));

        $payrolls = Payroll::query()
            ->with('employee:id,employee_id,first_name,last_name')
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where('pay_date', 'like', "%{$search}%")
                    ->orWhereHas('employee', function (Builder $query) use ($search): void {
                        $query->where('employee_id', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            })
            ->latest('pay_date')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('payrolls/index', [
            'payrolls' => $payrolls,
            'filters' => ['search' => $search],
        ]);
    }

    /**
     * Show the form for creating a new payroll record.
     */
    public function create(): Response
    {
        return Inertia::render('payrolls/form', [
            'payroll' => null,
            'employees' => $this->employeeOptions(),
        ]);
    }

    /**
     * Store a newly created payroll record.
     */
    public function store(PayrollRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['net_salary'] = round(
            (float) $data['basic_salary'] + (float) ($data['bonus'] ?? 0) - (float) ($data['deductions'] ?? 0),
            2,
        );

        Payroll::create($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Payroll created successfully.']);

        return to_route('payrolls.index');
    }

    /**
     * Show the form for editing the specified payroll record.
     */
    public function edit(Payroll $payroll): Response
    {
        return Inertia::render('payrolls/form', [
            'payroll' => [
                ...$payroll->toArray(),
                'pay_date' => $payroll->pay_date->format('Y-m-d'),
            ],
            'employees' => $this->employeeOptions(),
        ]);
    }

    /**
     * Update the specified payroll record.
     */
    public function update(PayrollRequest $request, Payroll $payroll): RedirectResponse
    {
        $data = $request->validated();
        $data['net_salary'] = round(
            (float) $data['basic_salary'] + (float) ($data['bonus'] ?? 0) - (float) ($data['deductions'] ?? 0),
            2,
        );

        $payroll->update($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Payroll updated successfully.']);

        return to_route('payrolls.index');
    }

    /**
     * Remove the specified payroll record.
     */
    public function destroy(Payroll $payroll): RedirectResponse
    {
        $payroll->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Payroll deleted successfully.']);

        return to_route('payrolls.index');
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
