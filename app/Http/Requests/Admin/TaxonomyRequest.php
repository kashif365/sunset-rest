<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Shared by dietary labels and allergens (same shape). */
class TaxonomyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-content') ?? false;
    }

    public function rules(): array
    {
        $table = $this->route()->getName() && str_contains($this->route()->getName(), 'allergen')
            ? 'allergens' : 'dietary_labels';

        $id = $this->route('dietary_label')?->id ?? $this->route('allergen')?->id;

        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:110', 'alpha_dash', Rule::unique($table, 'slug')->ignore($id)],
            'icon' => ['nullable', 'string', 'max:80'],
        ];
    }
}
