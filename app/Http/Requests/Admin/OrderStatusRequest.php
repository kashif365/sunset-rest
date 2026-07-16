<?php

namespace App\Http\Requests\Admin;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-orders') ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(Order::STATUSES)],
            'payment_status' => ['nullable', Rule::in(['unpaid', 'paid', 'refunded'])],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
