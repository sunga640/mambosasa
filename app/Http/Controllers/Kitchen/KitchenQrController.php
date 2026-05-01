<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Reception\Concerns\InteractsWithStaffScope;
use App\Models\KitchenRoomQr;
use App\Models\Room;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class KitchenQrController extends Controller
{
    use InteractsWithStaffScope;

    public function index(): View
    {
        $rooms = Room::query()->with('branch')->orderBy('name');
        $hasQrTable = Schema::hasTable('kitchen_room_qrs');
        $qrs = $hasQrTable ? KitchenRoomQr::query()->with(['room.branch'])->latest() : null;

        $ids = $this->scope()->branchIds();
        if ($ids !== null) {
            if ($ids === []) {
                $rooms->whereRaw('0=1');
                if ($qrs) {
                    $qrs->whereRaw('0=1');
                }
            } else {
                $rooms->whereIn('hotel_branch_id', $ids);
                if ($qrs) {
                    $qrs->whereIn('hotel_branch_id', $ids);
                }
            }
        }

        /** @var LengthAwarePaginator $paginatedRooms */
        $paginatedRooms = $rooms->paginate(9)->withQueryString();

        return view('kitchen.qr.index', [
            'rooms' => $paginatedRooms,
            'codes' => $qrs
                ? $qrs->whereIn('room_id', $paginatedRooms->getCollection()->pluck('id'))->get()->keyBy('room_id')
                : new Collection(),
            'hasQrTable' => $hasQrTable,
        ]);
    }

    public function store(Room $room): RedirectResponse
    {
        $this->ensureRoomInScope($room);

        abort_unless(Schema::hasTable('kitchen_room_qrs'), 503);

        KitchenRoomQr::query()->updateOrCreate(
            ['room_id' => $room->id],
            [
                'hotel_branch_id' => $room->hotel_branch_id,
                'token' => Str::lower(Str::random(40)),
                'is_active' => true,
            ]
        );

        return back()->with('status', __('Kitchen QR generated for :room.', ['room' => $room->name]));
    }
}
