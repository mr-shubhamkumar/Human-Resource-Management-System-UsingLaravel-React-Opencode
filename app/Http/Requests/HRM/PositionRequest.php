<?php

namespace App\Http\Requests\HRM;

use App\Models\Position;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PositionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'department_id' => ['required', 'exists:departments,id'],
            'title' => [
                'required',
                'string',
                'max:100',
                Rule::unique(Position::class)->where(
                    fn ($query) => $query->where('department_id', $this->input('department_id')),
                )->ignore($this->route('position')?->id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'salary_min' => ['required', 'numeric', 'min:0'],
            'salary_max' => ['required', 'numeric', 'min:0', 'gte:salary_min'],
        ];
    }
}
