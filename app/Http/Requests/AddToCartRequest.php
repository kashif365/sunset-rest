<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1', 'max:25'],
            'variation_id' => ['nullable', 'integer', 'exists:menu_item_variations,id'],
            'modifiers' => ['nullable', 'array', 'max:30'],
            'modifiers.*' => ['integer', 'exists:modifier_options,id'],
            'notes' => ['nullable', 'string', 'max:400'],
        ];
    }
}
