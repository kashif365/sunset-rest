<?php

namespace App\Http\Requests\Admin;

use App\Support\HtmlSanitizer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-content') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('page')?->id;

        return [
            'title' => ['required', 'string', 'max:190'],
            'slug' => ['required', 'string', 'max:190', 'alpha_dash', Rule::unique('pages', 'slug')->ignore($id)],
            'content' => ['nullable', 'string', 'max:60000'],
            'is_active' => ['boolean'],
            'seo_title' => ['nullable', 'string', 'max:190'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ];
    }

    /** Sanitized payload ready for persistence. */
    public function payload(): array
    {
        $data = $this->validated();
        $data['content'] = HtmlSanitizer::clean($data['content'] ?? null);
        $data['is_active'] = $this->boolean('is_active');

        return $data;
    }
}
