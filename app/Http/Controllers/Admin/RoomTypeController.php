<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HotelBranch;
use App\Models\MediaAsset;
use App\Models\RoomType;
use App\Support\UploadStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RoomTypeController extends Controller
{
    public function index(): View
    {
        return view('admin.room-types.index', [
            'roomTypes' => RoomType::query()->with(['branch', 'rooms'])->orderBy('name')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.room-types.create', [
            'branches' => HotelBranch::query()->orderBy('name')->get(),
            'mediaAssets' => MediaAsset::query()->latest()->limit(100)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $roomsToGenerate = (int) ($request->input('rooms_to_generate') ?: 0);
        if ($roomsToGenerate > 0 && empty($data['hotel_branch_id'])) {
            return back()->withInput()->withErrors(['hotel_branch_id' => __('Select branch when generating rooms automatically.')]);
        }
        DB::transaction(function () use ($data, $roomsToGenerate): void {
            $type = RoomType::query()->create($data);
            if ($roomsToGenerate > 0) {
                for ($i = 1; $i <= $roomsToGenerate; $i++) {
                    \App\Models\Room::query()->create([
                        'hotel_branch_id' => $type->hotel_branch_id,
                        'room_type_id' => $type->id,
                        'room_number' => 'R'.$type->id.'-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                        'floor_number' => 0,
                        'name' => $type->name.' '.$i,
                        'slug' => Str::slug($type->name.'-'.$i).'-'.Str::lower(Str::random(4)),
                        'status' => 'available',
                        'price' => $type->price,
                    ]);
                }
            }
        });

        return redirect()->route('admin.room-types.index')->with('status', __('Room type created.'));
    }

    public function edit(RoomType $roomType): View
    {
        return view('admin.room-types.edit', [
            'roomType' => $roomType,
            'branches' => HotelBranch::query()->orderBy('name')->get(),
            'mediaAssets' => MediaAsset::query()->latest()->limit(100)->get(),
        ]);
    }

    public function update(Request $request, RoomType $roomType): RedirectResponse
    {
        $data = $this->validated($request);
        $roomType->update($data);

        return redirect()->route('admin.room-types.index')->with('status', __('Room type updated.'));
    }

    public function destroy(RoomType $roomType): RedirectResponse
    {
        $roomType->delete();

        return redirect()->route('admin.room-types.index')->with('status', __('Room type deleted.'));
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'hotel_branch_id' => ['nullable', 'integer', 'exists:hotel_branches,id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0'],
            'max_rooms' => ['nullable', 'integer', 'min:0', 'max:5000'],
            'hero_image' => ['nullable', 'file', 'max:12288', 'extensions:jpg,jpeg,png,gif,webp,avif'],
            'hero_media_asset_id' => ['nullable', 'integer', 'exists:media_assets,id'],
            'thumbnail_media_asset_ids' => ['nullable', 'array', 'max:20'],
            'thumbnail_media_asset_ids.*' => ['nullable', 'integer', 'exists:media_assets,id'],
            'is_active' => ['nullable', 'boolean'],
            'rooms_to_generate' => ['nullable', 'integer', 'min:0', 'max:500'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['hotel_branch_id'] = $request->filled('hotel_branch_id') ? $request->integer('hotel_branch_id') : null;

        if ($request->file('hero_image')) {
            $validated['hero_image_path'] = UploadStorage::storePublic($request->file('hero_image'), 'room-types');
        } elseif ($request->filled('hero_media_asset_id')) {
            $validated['hero_image_path'] = MediaAsset::query()->whereKey($request->integer('hero_media_asset_id'))->value('path');
        }
        $validated['thumbnail_paths'] = MediaAsset::query()
            ->whereIn('id', array_values(array_filter(array_map('intval', $request->input('thumbnail_media_asset_ids', [])))))
            ->pluck('path')
            ->values()
            ->all();

        unset($validated['hero_image'], $validated['hero_media_asset_id'], $validated['thumbnail_media_asset_ids']);

        return $validated;
    }
}
