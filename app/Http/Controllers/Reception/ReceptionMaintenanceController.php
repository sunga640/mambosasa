<?php

namespace App\Http\Controllers\Reception;

use App\Enums\MaintenanceKind;
use App\Enums\MaintenanceStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reception\Concerns\InteractsWithStaffScope;
use App\Http\Requests\Admin\StoreRoomMaintenanceRequest;
use App\Http\Requests\Admin\UpdateRoomMaintenanceRequest;
use App\Models\HotelBranch;
use App\Models\Room;
use App\Models\RoomMaintenance;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReceptionMaintenanceController extends Controller
{
    use InteractsWithStaffScope;

    public function index(Request $request): View
    {
        $query = RoomMaintenance::query()->with(['room', 'branch'])->latest();

        $ids = $this->scope()->branchIds();
        if ($ids !== null) {
            if ($ids === []) {
                $query->whereRaw('0=1');
            } else {
                $query->whereIn('hotel_branch_id', $ids);
            }
        }

        if ($request->filled('branch_id')) {
            $query->where('hotel_branch_id', $request->integer('branch_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return view('reception.maintenance.index', [
            'records' => $query->paginate(7)->withQueryString(),
            'branches' => HotelBranch::query()->orderBy('name')->get(),
            'filterBranchId' => $request->integer('branch_id') ?: null,
            'filterStatus' => $request->string('status')->toString() ?: null,
            'kinds' => MaintenanceKind::cases(),
            'statuses' => MaintenanceStatus::cases(),
        ]);
    }

    public function create(Request $request): View
    {
        $branchId = $request->integer('branch_id') ?: null;
        $ids = $this->scope()->branchIds();

        $roomsQuery = Room::query()
            ->when($branchId, fn ($q) => $q->where('hotel_branch_id', $branchId))
            ->with('branch')
            ->orderBy('name');

        if ($ids !== null) {
            if ($ids === []) {
                $roomsQuery->whereRaw('0=1');
            } else {
                $roomsQuery->whereIn('hotel_branch_id', $ids);
            }
        }

        return view('reception.maintenance.create', [
            'branches' => HotelBranch::query()->orderBy('name')->get(),
            'rooms' => $roomsQuery->get(),
            'selectedBranchId' => $branchId,
            'kinds' => MaintenanceKind::cases(),
            'statuses' => MaintenanceStatus::cases(),
        ]);
    }

    public function store(StoreRoomMaintenanceRequest $request): RedirectResponse
    {
        $room = Room::query()->findOrFail($request->validated('room_id'));
        $this->ensureRoomInScope($room);

        $data = $request->validated();
        $m = RoomMaintenance::query()->create([
            'room_id' => $room->id,
            'hotel_branch_id' => $room->hotel_branch_id,
            'kind' => $data['kind'],
            'description' => $data['description'] ?? null,
            'expenses' => $data['expenses'],
            'started_at' => $request->date('started_at') ?? now(),
            'due_at' => $request->date('due_at'),
            'status' => $data['status'],
            'created_by' => $request->user()->id,
        ]);

        ActivityLogger::log('maintenance.created', $request->user(), RoomMaintenance::class, $m->id);

        return redirect()->route('reception.maintenance.index')->with('status', __('Maintenance record created.'));
    }

    public function edit(RoomMaintenance $maintenance): View
    {
        $this->ensureMaintenanceInScope($maintenance);
        $maintenance->load(['room', 'branch']);

        return view('reception.maintenance.edit', [
            'maintenance' => $maintenance,
            'kinds' => MaintenanceKind::cases(),
            'statuses' => MaintenanceStatus::cases(),
        ]);
    }

    public function update(UpdateRoomMaintenanceRequest $request, RoomMaintenance $maintenance): RedirectResponse
    {
        $this->ensureMaintenanceInScope($maintenance);

        $data = $request->validated();
        if ($request->filled('started_at')) {
            $data['started_at'] = $request->date('started_at');
        }
        $data['due_at'] = $request->filled('due_at') ? $request->date('due_at') : null;
        $data['completed_at'] = $request->filled('completed_at') ? $request->date('completed_at') : null;
        $maintenance->update($data);

        return redirect()->route('reception.maintenance.index')->with('status', __('Maintenance updated.'));
    }

    public function destroy(RoomMaintenance $maintenance): RedirectResponse
    {
        $this->ensureMaintenanceInScope($maintenance);
        $maintenance->delete();

        return redirect()->route('reception.maintenance.index')->with('status', __('Record deleted.'));
    }
}
