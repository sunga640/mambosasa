<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreHotelBranchRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'location_address' => ['nullable', 'string', 'max:2000'],
            'city' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:120'],
            'is_ground_floor_only' => ['sometimes', 'boolean'],
            'floors_count' => ['nullable', 'integer', 'min:1', 'max:200'],
            'contact_phone' => ['nullable', 'string', 'max:80'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_whatsapp' => ['nullable', 'string', 'max:80'],
            'extra_notes' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['sometimes', 'boolean'],
            'logo' => ['nullable', 'file', 'max:4096', 'extensions:jpg,jpeg,png,gif,svg,webp,avif'],
            'logo_media_asset_id' => ['nullable', 'integer', 'exists:media_assets,id'],
            'preview_images' => ['nullable', 'array', 'max:4'],
            'preview_images.*' => ['nullable', 'file', 'max:4096', 'extensions:jpg,jpeg,png,gif,svg,webp,avif'],
            'preview_media_asset_ids' => ['nullable', 'array', 'max:4'],
            'preview_media_asset_ids.*' => ['nullable', 'integer', 'exists:media_assets,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_ground_floor_only' => $this->boolean('is_ground_floor_only'),
            'is_active' => $this->boolean('is_active', true),
            'floors_count' => $this->boolean('is_ground_floor_only') ? 1 : (int) ($this->input('floors_count') ?: 1),
        ]);
    }
}
