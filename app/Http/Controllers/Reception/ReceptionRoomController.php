<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Reception\Concerns\InteractsWithStaffScope;
use App\Http\Requests\Admin\StoreRoomRequest;
use App\Http\Requests\Admin\UpdateRoomRequest;
use App\Models\HotelBranch;
use App\Models\Role;
use App\Models\Room;
use App\Models\RoomType;
use App\Services\Admin\RoomService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;

class ReceptionRoomController extends Controller
{
    use InteractsWithStaffScope;

    public function __construct(
        private RoomService $rooms,
    ) {}

    public function index(Request $request): View
    {
        $filterRoomTypeId = $request->integer('room_type_id') ?: null;
        $availableOnly = $request->boolean('available_only');
        $checkIn = $request->date('check_in');
        $checkOut = $request->date('check_out');

        $query = Room::query()
            ->with([
                'branch',
                'type',
                'bookings' => function ($q): void {
                    $q->with('invoice')
                        ->whereIn('status', [\App\Enums\BookingStatus::PendingPayment->value, \App\Enums\BookingStatus::Confirmed->value])
                        ->orderByDesc('updated_at')
                        ->limit(8);
                },
            ])
            ->orderByDesc('id');

        $this->scope()->filterRoomsByBranch($query);

        if ($filterRoomTypeId) {
            $query->where('room_type_id', $filterRoomTypeId);
        }

        if ($request->filled('branch_id')) {
            $query->where('hotel_branch_id', $request->integer('branch_id'));
        }

        if ($availableOnly && $checkIn && $checkOut && $checkOut->gt($checkIn)) {
            $query->where('force_in_use', false)
                ->whereDoesntHave('bookings', function ($bq) use ($checkIn, $checkOut): void {
                    $bq->whereIn('status', [\App\Enums\BookingStatus::PendingPayment->value, \App\Enums\BookingStatus::Confirmed->value])
                        ->whereDate('check_in', '<', $checkOut->toDateString())
                        ->whereDate('check_out', '>', $checkIn->toDateString());
                });
        }

        $roomTypeQuery = RoomType::query()->where('is_active', true)->orderBy('name');
        $branchIds = $this->scope()->branchIds();
        if ($branchIds !== null) {
            if ($branchIds === []) {
                $roomTypeQuery->whereRaw('0 = 1');
            } else {
                $roomTypeQuery->where(function ($rq) use ($branchIds): void {
                    $rq->whereNull('hotel_branch_id')->orWhereIn('hotel_branch_id', $branchIds);
                });
            }
        }

        return view('reception.rooms.index', [
            'rooms' => $query->paginate(7)->withQueryString(),
            'branches' => $this->branchesForForm(),
            'roomTypes' => $roomTypeQuery->get(),
            'filterBranchId' => $request->integer('branch_id') ?: null,
            'filterRoomTypeId' => $filterRoomTypeId,
            'availableOnly' => $availableOnly,
            'filterCheckIn' => $request->input('check_in'),
            'filterCheckOut' => $request->input('check_out'),
        ]);
    }

    public function create(Request $request): View
    {
        $branches = $this->branchesForForm();
        $selected = $request->integer('branch_id') ?: null;
        if ($branches->count() === 1) {
            $selected = $branches->first()->id;
        }

        return view('reception.rooms.create', [
            'branches' => $branches,
            'selectedBranchId' => $selected,
            'roomTypes' => RoomType::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreRoomRequest $request): RedirectResponse
    {
        $bid = (int) $request->validated('hotel_branch_id');
        $ids = $this->scope()->branchIds();
        if ($ids !== null && ($ids === [] || ! in_array($bid, $ids, true))) {
            abort(403);
        }

        $branch = HotelBranch::query()->findOrFail($bid);
        $attributes = collect($request->safe()->all())
            ->except(['video', 'hero_image', 'images', 'image_captions'])
            ->all();
        $roomType = RoomType::query()->find($attributes['room_type_id'] ?? null);
        if ($roomType && $roomType->max_rooms > 0) {
            $current = Room::query()->where('room_type_id', $roomType->id)->count();
            if ($current >= $roomType->max_rooms) {
                return back()->withInput()->withErrors(['room_type_id' => __('Count full for this room type. Increase room type count first.')]);
            }
        }
        [$images, $imageCaptions] = $this->pairedGalleryUploads(
            $request->file('images', []) ?? [],
            $request->input('image_captions', []),
        );

        $this->rooms->create(
            $branch,
            $attributes,
            $request->file('video'),
            $request->file('hero_image'),
            $images,
            $imageCaptions,
        );

        return redirect()->route('reception.rooms.index')->with('status', __('Room created.'));
    }

    public function edit(Room $room): View
    {
        $this->ensureRoomInScope($room);

        return view('reception.rooms.edit', [
            'room' => $room->load(['images', 'branch']),
            'branches' => $this->branchesForForm(),
            'roomTypes' => RoomType::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateRoomRequest $request, Room $room): RedirectResponse
    {
        $this->ensureRoomInScope($room);

        $bid = (int) $request->validated('hotel_branch_id');
        $ids = $this->scope()->branchIds();
        if ($ids !== null && ($ids === [] || ! in_array($bid, $ids, true))) {
            abort(403);
        }

        $attributes = collect($request->safe()->all())
            ->except(['video', 'hero_image', 'images', 'remove_video', 'remove_hero_image', 'remove_image_ids', 'image_captions', 'captions'])
            ->all();
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
        );

        return redirect()->route('reception.rooms.index')->with('status', __('Room updated.'));
    }

    public function destroy(Room $room): RedirectResponse
    {
        $this->ensureRoomInScope($room);
        $this->rooms->delete($room);

        return redirect()->route('reception.rooms.index')->with('status', __('Room deleted.'));
    }

    public function toggleInUse(Room $room): RedirectResponse
    {
        $this->ensureRoomInScope($room);
        $room->update(['force_in_use' => ! $room->force_in_use]);

        return back()->with('status', __('Room occupancy flag updated.'));
    }

    /**
     * @return \Illuminate\Support\Collection<int, HotelBranch>
     */
    private function branchesForForm()
    {
        $user = auth()->user();
        if ($user?->role?->slug === Role::RECEPTION_SLUG && $user->hotel_branch_id) {
            return HotelBranch::query()->whereKey($user->hotel_branch_id)->orderBy('name')->get();
        }

        return HotelBranch::query()->orderBy('name')->get();
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
