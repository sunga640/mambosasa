<?php

namespace App\Http\Requests\Admin;

use App\Enums\RoomStatus;
use App\Models\HotelBranch;
use App\Models\Room;
use App\Models\RoomImage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $room = $this->route('room');
        $roomId = $room instanceof Room ? $room->id : 0;

        return [
            'hotel_branch_id' => ['required', 'exists:hotel_branches,id'],
            'room_type_id' => ['required', 'integer', 'exists:room_types,id'],
            'room_rank_id' => ['nullable', 'integer', 'exists:room_ranks,id'],
            'room_number' => ['required', 'string', 'max:32'],
            'floor_number' => ['required', 'integer', 'min:0', 'max:254'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(RoomStatus::values())],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'description' => ['nullable', 'string', 'max:10000'],
            'card_primary' => ['nullable', Rule::in(['none', 'image', 'video'])],
            'hero_image' => ['nullable', 'file', 'max:12288', 'extensions:jpg,jpeg,png,gif,webp,avif'],
            'hero_media_asset_id' => ['nullable', 'integer', 'exists:media_assets,id'],
            'video' => ['nullable', 'file', 'max:51200', 'extensions:mp4,webm,mov'],
            'remove_video' => ['sometimes', 'boolean'],
            'remove_hero_image' => ['sometimes', 'boolean'],
            'images' => ['nullable', 'array', 'max:20'],
            'images.*' => ['nullable', 'file', 'max:12288', 'extensions:jpg,jpeg,png,gif,webp,avif'],
            'gallery_media_asset_ids' => ['nullable', 'array', 'max:30'],
            'gallery_media_asset_ids.*' => ['nullable', 'integer', 'exists:media_assets,id'],
            'remove_image_ids' => ['nullable', 'array'],
            'remove_image_ids.*' => [
                'integer',
                Rule::exists('room_images', 'id')->where('room_id', $roomId),
            ],
            'image_captions' => ['nullable', 'array'],
            'image_captions.*' => ['nullable', 'string', 'max:160'],
            'captions' => ['nullable', 'array'],
            'captions.*' => ['nullable', 'string', 'max:160'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $roomRank = $this->input('room_rank_id');
        if ($roomRank === '' || $roomRank === null) {
            $this->merge(['room_rank_id' => null]);
        }
        $this->merge([
            'remove_video' => $this->boolean('remove_video'),
            'remove_hero_image' => $this->boolean('remove_hero_image'),
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $branch = HotelBranch::query()->find($this->integer('hotel_branch_id'));
            if (! $branch) {
                return;
            }
            $floor = $this->integer('floor_number');
            if ($floor > $branch->maxFloorIndex()) {
                $validator->errors()->add(
                    'floor_number',
                    __('This floor is not valid for the selected branch (max floor index: :n).', ['n' => $branch->maxFloorIndex()])
                );
            }

            $room = $this->route('room');
            if ($room instanceof Room) {
                foreach (array_keys($this->input('captions', [])) as $key) {
                    if (! is_numeric($key)) {
                        continue;
                    }
                    if (! RoomImage::query()->where('room_id', $room->id)->whereKey((int) $key)->exists()) {
                        $validator->errors()->add('captions', __('Invalid gallery image.'));

                        return;
                    }
                }
            }
        });
    }
}
