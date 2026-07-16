<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HolidayHourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-settings') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('holiday_hour')?->id;

        return [
            'date' => ['required', 'date', Rule::unique('holiday_hours', 'date')->ignore($id)],
            'label' => ['required', 'string', 'max:190'],
            'is_closed' => ['boolean'],
            'open_time' => ['nullable', 'required_if:is_closed,0', 'date_format:H:i'],
            'close_time' => ['nullable', 'required_if:is_closed,0', 'date_format:H:i', 'after:open_time'],
        ];
    }
}
