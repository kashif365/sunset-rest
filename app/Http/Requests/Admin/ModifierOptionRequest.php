<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ModifierOptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-content') ?? false;
    }

    public function rules(): array
    {
        return [
            'modifier_group_id' => ['required', 'integer', 'exists:modifier_groups,id'],
            'name' => ['required', 'string', 'max:150'],
            'price_adjustment' => ['nullable', 'numeric', 'min:-100', 'max:999'],
            'is_default' => ['boolean'],
            'is_available' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65000'],
        ];
    }
}
