<?php

namespace App\Http\Requests\HRM;

use App\Models\Attendance;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttendanceRequest extends FormRequest
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
            'date' => [
                'required',
                'date',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $exists = Attendance::query()
                        ->where('employee_id', $this->input('employee_id'))
                        ->whereDate('date', $value)
                        ->when(
                            $this->route('attendance'),
                            fn ($query, $attendance) => $query->whereKeyNot($attendance->id),
                        )
                        ->exists();

                    if ($exists) {
                        $fail('An attendance record for this employee and date already exists.');
                    }
                },
            ],
            'check_in' => ['nullable', 'date_format:H:i'],
            'check_out' => ['nullable', 'date_format:H:i'],
            'status' => ['required', Rule::in(['present', 'late', 'absent'])],
        ];
    }
}
