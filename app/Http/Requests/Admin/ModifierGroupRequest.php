<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ModifierGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-content') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'selection_type' => ['required', 'in:single,multiple'],
            'min_select' => ['nullable', 'integer', 'min:0', 'max:30'],
            'max_select' => ['nullable', 'integer', 'min:1', 'max:30', 'gte:min_select'],
            'is_required' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65000'],
        ];
    }
}
