<?php

namespace App\Http\Requests;

use App\Services\Payments\PaymentManager;
use App\Services\PickupSlotService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Validator;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:190'],
            'customer_phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\().\s]{7,}$/'],
            'pickup_date' => ['required', 'date_format:Y-m-d'],
            'pickup_time' => ['required', 'date_format:H:i'],
            'payment_method' => ['required', 'string'],
            'tip' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $payments = app(PaymentManager::class);

            if (! $payments->isValid($this->input('payment_method'))) {
                $validator->errors()->add('payment_method', 'Please choose a valid payment option.');
            }

            // The date/time slot checks depend on pickup_date and pickup_time
            // already being well-formed — skip them if those fields failed
            // their own format rules, so we don't feed garbage into Carbon.
            if ($validator->errors()->has('pickup_date') || $validator->errors()->has('pickup_time')) {
                return;
            }

            $slots = app(PickupSlotService::class);
            $date = Carbon::createFromFormat('Y-m-d', $this->input('pickup_date'));

            if (! in_array($this->input('pickup_date'), $slots->orderableDates(), true)) {
                $validator->errors()->add('pickup_date', 'We are not accepting pickup orders for that date.');
            } elseif (! $slots->isValidSlot($date, $this->input('pickup_time'))) {
                $validator->errors()->add('pickup_time', 'That pickup time is not available. Please pick another slot.');
            }
        });
    }
}
