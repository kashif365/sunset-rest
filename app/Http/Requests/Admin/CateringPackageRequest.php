<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CateringPackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-content') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('catering_package')?->id;

        return [
            'name' => ['required', 'string', 'max:190'],
            'slug' => ['required', 'string', 'max:190', 'alpha_dash', Rule::unique('catering_packages', 'slug')->ignore($id)],
            'description' => ['nullable', 'string', 'max:4000'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:99999'],
            'price_label' => ['nullable', 'string', 'max:60'],
            'serves' => ['nullable', 'string', 'max:120'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'image_alt' => ['nullable', 'string', 'max:190'],
            'needs_verification' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65000'],
        ];
    }
}
