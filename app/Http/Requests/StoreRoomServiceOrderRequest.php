<?php

namespace App\Http\Requests;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\RestaurantMenuItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreRoomServiceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_item_id' => ['required', 'integer', 'exists:restaurant_menu_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:20'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $user = $this->user();
            if (! $user) {
                return;
            }

            $roomId = (int) $this->input('room_id');
            $ok = Booking::query()
                ->where('user_id', $user->id)
                ->where('room_id', $roomId)
                ->where('status', BookingStatus::Confirmed)
                ->whereDate('check_in', '<=', now()->toDateString())
                ->whereDate('check_out', '>', now()->toDateString())
                ->exists();

            if (! $ok) {
                $validator->errors()->add('room_id', __('You can only order for a room you are currently staying in.'));
            }

            if (count($this->parsedLineItems()) < 1) {
                $validator->errors()->add('items', __('Select at least one menu item with quantity greater than zero.'));
            }
        });
    }

    /**
     * @return list<array{menu_item_id: int, quantity: int}>
     */
    public function parsedLineItems(): array
    {
        $out = [];
        foreach ($this->input('items', []) as $row) {
            if (! is_array($row)) {
                continue;
            }
            $mid = (int) ($row['menu_item_id'] ?? 0);
            $qty = (int) ($row['quantity'] ?? 0);
            if ($mid > 0 && $qty > 0) {
                $out[] = ['menu_item_id' => $mid, 'quantity' => $qty];
            }
        }

        return $out;
    }

    /**
     * @return list<array{menu_item_id: int, quantity: int}>
     */
    public function validatedItems(): array
    {
        return $this->parsedLineItems();
    }
}
