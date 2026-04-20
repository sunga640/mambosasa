<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoomRequest;
use App\Http\Requests\Admin\UpdateRoomRequest;
use App\Models\HotelBranch;
use App\Models\MediaAsset;
use App\Models\Room;
use App\Models\RoomRank;
use App\Models\RoomType;
use App\Services\Admin\RoomService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function __construct(
        private RoomService $rooms,
    ) {}

    public function index(Request $request): View
    {
        $filterRoomTypeId = $request->integer('room_type_id') ?: null;
        $sessionBranchId = session('director_branch_id') ? (int) session('director_branch_id') : null;
        $userBranchId = auth()->user()?->hotel_branch_id ? (int) auth()->user()->hotel_branch_id : null;
        $effectiveBranchId = $request->integer('branch_id') ?: ($userBranchId ?: $sessionBranchId);
        if (! $effectiveBranchId) {
            $effectiveBranchId = (int) HotelBranch::query()->orderBy('name')->value('id');
        }

        $query = Room::query()
            ->with([
                'branch',
                'rank',
                'bookings' => function ($q): void {
                    $q->with('invoice')
                        ->whereIn('status', [\App\Enums\BookingStatus::PendingPayment->value, \App\Enums\BookingStatus::Confirmed->value])
                        ->orderByDesc('updated_at')
                        ->limit(8);
                },
            ])
            ->orderByDesc('id');

        $query->where('hotel_branch_id', $effectiveBranchId);
        if ($request->filled('room_type_id')) {
            $query->where('room_type_id', $request->integer('room_type_id'));
        }

        $currentBranchName = HotelBranch::query()->whereKey($effectiveBranchId)->value('name') ?? __('Unknown branch');
        $currentRoomTypeName = $filterRoomTypeId
            ? RoomType::query()->whereKey($filterRoomTypeId)->value('name')
            : null;

        return view('admin.rooms.index', [
            'rooms' => $query->paginate(7)->withQueryString(),
            'branches' => HotelBranch::query()->orderBy('name')->get(),
            'filterBranchId' => $effectiveBranchId,
            'filterRoomTypeId' => $filterRoomTypeId,
            'currentBranchName' => $currentBranchName,
            'currentRoomTypeName' => $currentRoomTypeName,
        ]);
    }

    public function create(Request $request): View
    {
        $fixedBranchId = auth()->user()?->hotel_branch_id ?: null;
        $scopedBranchId = session('director_branch_id') ? (int) session('director_branch_id') : null;
        $selectedRoomTypeId = $request->integer('room_type_id') ?: null;
        return view('admin.rooms.create', [
            'branches' => HotelBranch::query()->orderBy('name')->get(),
            'roomRanks' => RoomRank::query()->orderBy('sort_order')->orderBy('name')->get(),
            'roomTypes' => RoomType::query()->where('is_active', true)->orderBy('name')->get(),
            'selectedBranchId' => $fixedBranchId ?: ($scopedBranchId ?: ($request->integer('branch_id') ?: null)),
            'selectedRoomTypeId' => $selectedRoomTypeId,
            'fixedBranchId' => $fixedBranchId,
            'mediaAssets' => MediaAsset::query()->latest()->limit(100)->get(),
        ]);
    }

    public function store(StoreRoomRequest $request): RedirectResponse
    {
        $effectiveBranchId = auth()->user()?->hotel_branch_id ?: (int) $request->validated('hotel_branch_id');
        $branch = HotelBranch::query()->findOrFail($effectiveBranchId);
        $attributes = collect($request->safe()->all())
            ->except(['video', 'hero_image', 'images', 'image_captions'])
            ->all();
        $attributes['hotel_branch_id'] = $effectiveBranchId;
        [$images, $imageCaptions] = $this->pairedGalleryUploads(
            $request->file('images', []) ?? [],
            $request->input('image_captions', []),
        );

        $roomType = RoomType::query()->find($attributes['room_type_id'] ?? null);
        if ($roomType && $roomType->max_rooms > 0) {
            $current = Room::query()->where('room_type_id', $roomType->id)->count();
            if ($current >= $roomType->max_rooms) {
                return back()->withInput()->withErrors(['room_type_id' => __('Count full for this room type. Increase room type count first.')]);
            }
        }

        $this->rooms->create(
            $branch,
            $attributes,
            $request->file('video'),
            $request->file('hero_image'),
            $images,
            $imageCaptions,
            $request->integer('hero_media_asset_id') ?: null,
            array_values(array_filter(array_map('intval', $request->input('gallery_media_asset_ids', [])))),
        );

        return redirect()->route('admin.rooms.index')->with('status', __('Room created.'));
    }

    public function edit(Room $room): View
    {
        return view('admin.rooms.edit', [
            'room' => $room->load(['images', 'branch', 'rank']),
            'branches' => HotelBranch::query()->orderBy('name')->get(),
            'roomRanks' => RoomRank::query()->orderBy('sort_order')->orderBy('name')->get(),
            'roomTypes' => RoomType::query()->where('is_active', true)->orderBy('name')->get(),
            'mediaAssets' => MediaAsset::query()->latest()->limit(100)->get(),
        ]);
    }

    public function update(UpdateRoomRequest $request, Room $room): RedirectResponse
    {
        $fallbackBranchId = (int) $room->hotel_branch_id;
        $attributes = collect($request->safe()->all())
            ->except(['video', 'hero_image', 'images', 'remove_video', 'remove_hero_image', 'remove_image_ids', 'image_captions', 'captions'])
            ->all();
        $attributes['hotel_branch_id'] = auth()->user()?->hotel_branch_id ?: (int) ($attributes['hotel_branch_id'] ?? $room->hotel_branch_id);
        $redirectBranchId = (int) ($attributes['hotel_branch_id'] ?? $fallbackBranchId);
        [$images, $newCaptions] = $this->pairedGalleryUploads(
            $request->file('images', []) ?? [],
            $request->input('image_captions', []),
        );
        $removeIds = array_map('intval', $request->validated('remove_image_ids', []));
        $captionUpdates = $request->input('captions', []);

        $this->rooms->update(
            $room,
            $attributes,
            $request->file('video'),
            $request->boolean('remove_video'),
            $request->file('hero_image'),
            $request->boolean('remove_hero_image'),
            $images,
            $removeIds,
            $newCaptions,
            is_array($captionUpdates) ? $captionUpdates : [],
            $request->integer('hero_media_asset_id') ?: null,
            array_values(array_filter(array_map('intval', $request->input('gallery_media_asset_ids', [])))),
        );

        return redirect()->route('admin.rooms.index', ['branch_id' => $redirectBranchId])->with('status', __('Room updated.'));
    }

    public function destroy(Room $room): RedirectResponse
    {
        $this->rooms->delete($room);

        return redirect()->route('admin.rooms.index')->with('status', __('Room deleted.'));
    }

    public function toggleInUse(Room $room): RedirectResponse
    {
        $room->update(['force_in_use' => ! $room->force_in_use]);

        return back()->with('status', __('Room occupancy flag updated.'));
    }

    /**
     * @param  array<int, mixed>  $files
     * @param  array<int, mixed>  $captions
     * @return array{0: list<UploadedFile>, 1: list<string|null>}
     */
    private function pairedGalleryUploads(array $files, array $captions): array
    {
        $images = [];
        $outCaps = [];

        foreach ($files as $i => $f) {
            if ($f instanceof UploadedFile && $f->isValid()) {
                $images[] = $f;
                $c = $captions[$i] ?? null;
                $outCaps[] = is_string($c) ? $c : null;
            }
        }

        return [$images, $outCaps];
    }
}
