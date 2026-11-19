<?php

namespace App\Http\Requests\HRM;

use App\Models\Employee;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $employeeId = $this->route('employee')?->id;

        return [
            'employee_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique(Employee::class)->ignore($employeeId),
            ],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique(Employee::class)->ignore($employeeId),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'gender' => ['nullable', Rule::in(['Male', 'Female', 'Other'])],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:1000'],
            'hire_date' => ['required', 'date'],
            'department_id' => ['required', 'exists:departments,id'],
            'position_id' => ['required', 'exists:positions,id'],
            'status' => ['required', Rule::in(['active', 'inactive', 'terminated'])],
            'is_approved' => ['boolean'],
            'profile_photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
