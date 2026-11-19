<?php

namespace App\Http\Requests\HRM;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PayslipRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payroll_id' => ['required', 'exists:payrolls,id'],
            'generated_at' => ['nullable', 'date'],
            'file_path' => ['required', 'string', 'max:255'],
        ];
    }
}
