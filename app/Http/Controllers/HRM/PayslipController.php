<?php

namespace App\Http\Controllers\HRM;

use App\Http\Controllers\Controller;
use App\Http\Requests\HRM\PayslipRequest;
use App\Models\Payroll;
use App\Models\Payslip;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PayslipController extends Controller
{
    /**
     * Display a paginated, searchable list of payslips.
     */
    public function index(Request $request): Response
    {
        $search = trim((string) $request->input('search', ''));

        $payslips = Payslip::query()
            ->with('payroll.employee:id,employee_id,first_name,last_name')
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where('file_path', 'like', "%{$search}%")
                    ->orWhereHas('payroll.employee', function (Builder $query) use ($search): void {
                        $query->where('employee_id', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            })
            ->latest('generated_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('payslips/index', [
            'payslips' => $payslips,
            'filters' => ['search' => $search],
        ]);
    }

    /**
     * Show the form for creating a new payslip.
     */
    public function create(): Response
    {
        return Inertia::render('payslips/form', [
            'payslip' => null,
            'payrolls' => $this->payrollOptions(),
            'defaultFilePath' => 'payslips/'.now()->format('Y/m').'/payslip-'.now()->format('YmdHis').'.pdf',
        ]);
    }

    /**
     * Store a newly created payslip.
     */
    public function store(PayslipRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['generated_at'] = $data['generated_at'] ?? now();

        Payslip::create($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Payslip created successfully.']);

        return to_route('payslips.index');
    }

    /**
     * Show the form for editing the specified payslip.
     */
    public function edit(Payslip $payslip): Response
    {
        return Inertia::render('payslips/form', [
            'payslip' => [
                ...$payslip->toArray(),
                'generated_at' => $payslip->generated_at->format('Y-m-d\TH:i'),
            ],
            'payrolls' => $this->payrollOptions(),
            'defaultFilePath' => $payslip->file_path,
        ]);
    }

    /**
     * Update the specified payslip.
     */
    public function update(PayslipRequest $request, Payslip $payslip): RedirectResponse
    {
        $data = $request->validated();
        $data['generated_at'] = $data['generated_at'] ?? $payslip->generated_at;

        $payslip->update($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Payslip updated successfully.']);

        return to_route('payslips.index');
    }

    /**
     * Remove the specified payslip.
     */
    public function destroy(Payslip $payslip): RedirectResponse
    {
        $payslip->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Payslip deleted successfully.']);

        return to_route('payslips.index');
    }

    /**
     * Get payrolls for the selection field.
     *
     * @return array<int, array<string, mixed>>
     */
    private function payrollOptions(): array
    {
        return Payroll::query()
            ->with('employee:id,employee_id,first_name,last_name')
            ->orderByDesc('pay_date')
            ->limit(200)
            ->get()
            ->map(fn (Payroll $payroll): array => [
                'id' => $payroll->id,
                'label' => $payroll->employee->employee_id.' - '.$payroll->pay_date->format('M Y'),
            ])
            ->all();
    }
}
