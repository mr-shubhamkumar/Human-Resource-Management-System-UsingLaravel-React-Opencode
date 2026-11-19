<?php

namespace App\Http\Requests\HRM;

use App\Models\Department;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique(Department::class)->ignore($this->route('department')?->id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_approved' => ['boolean'],
        ];
    }
}
