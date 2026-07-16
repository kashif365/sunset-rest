<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BusinessHoursRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-settings') ?? false;
    }

    public function rules(): array
    {
        return [
            'hours' => ['required', 'array', 'size:7'],
            'hours.*.day_of_week' => ['required', 'integer', 'between:0,6'],
            'hours.*.is_closed' => ['boolean'],
            'hours.*.open_time' => ['nullable', 'required_unless:hours.*.is_closed,1', 'date_format:H:i'],
            'hours.*.close_time' => ['nullable', 'required_unless:hours.*.is_closed,1', 'date_format:H:i', 'after:hours.*.open_time'],
        ];
    }
}
