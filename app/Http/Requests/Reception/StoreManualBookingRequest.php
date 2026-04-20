<?php

namespace App\Http\Requests\Reception;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreManualBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isReceptionStaff() ?? false;
    }

    public function rules(): array
    {
        return [
            'room_id' => ['required', 'integer', Rule::exists('rooms', 'id')],
            'booking_method_id' => ['required', 'integer', Rule::exists('booking_methods', 'id')],
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:64'],
            'rooms_count' => ['required', 'integer', 'min:1', 'max:20'],
            'special_requests' => ['nullable', 'string', 'max:2000'],
            'confirm_paid' => ['sometimes', 'boolean'],
        ];
    }
}
