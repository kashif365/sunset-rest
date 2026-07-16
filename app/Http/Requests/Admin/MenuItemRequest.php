<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-content') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('menu_item')?->id;

        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:190'],
            'slug' => ['required', 'string', 'max:190', 'alpha_dash', Rule::unique('menu_items', 'slug')->ignore($id)],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999'],
            'discounted_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'image_alt' => ['nullable', 'string', 'max:190'],
            'prep_time_minutes' => ['nullable', 'integer', 'min:0', 'max:600'],
            'stock_quantity' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'is_available' => ['boolean'],
            'is_sold_out' => ['boolean'],
            'is_featured' => ['boolean'],
            'is_bestseller' => ['boolean'],
            'needs_verification' => ['boolean'],
            'available_from' => ['nullable', 'date_format:H:i'],
            'available_until' => ['nullable', 'date_format:H:i', 'after:available_from'],
            'available_days' => ['nullable', 'array'],
            'available_days.*' => ['integer', 'between:0,6'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65000'],
            'seo_title' => ['nullable', 'string', 'max:190'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'dietary_labels' => ['nullable', 'array'],
            'dietary_labels.*' => ['integer', 'exists:dietary_labels,id'],
            'allergens' => ['nullable', 'array'],
            'allergens.*' => ['integer', 'exists:allergens,id'],
            'modifier_groups' => ['nullable', 'array'],
            'modifier_groups.*' => ['integer', 'exists:modifier_groups,id'],
            // Inline variations editor
            'variations' => ['nullable', 'array', 'max:20'],
            'variations.*.id' => ['nullable', 'integer'],
            'variations.*.name' => ['required_with:variations.*.price', 'string', 'max:120'],
            'variations.*.price' => ['required_with:variations.*.name', 'numeric', 'min:0', 'max:9999'],
        ];
    }
}
