<?php

namespace App\Http\Requests\HRM;

use App\Models\Payroll;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PayrollRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'bonus' => ['nullable', 'numeric', 'min:0'],
            'deductions' => ['nullable', 'numeric', 'min:0'],
            'pay_date' => [
                'required',
                'date',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $exists = Payroll::query()
                        ->where('employee_id', $this->input('employee_id'))
                        ->whereDate('pay_date', $value)
                        ->when(
                            $this->route('payroll'),
                            fn ($query, $payroll) => $query->whereKeyNot($payroll->id),
                        )
                        ->exists();

                    if ($exists) {
                        $fail('A payroll record for this employee and pay date already exists.');
                    }
                },
            ],
        ];
    }
}
