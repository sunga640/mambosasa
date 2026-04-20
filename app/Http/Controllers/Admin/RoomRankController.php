<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomRank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoomRankController extends Controller
{
    public function index(): View
    {
        return view('admin.room-ranks.index', [
            'ranks' => RoomRank::query()->orderBy('sort_order')->paginate(7),
        ]);
    }

    public function create(): View
    {
        return view('admin.room-ranks.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:80', 'regex:/^[a-z0-9\-]+$/', Rule::unique('room_ranks', 'slug')],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        RoomRank::query()->create($data);

        return redirect()->route('admin.room-ranks.index')->with('status', __('Room rank created.'));
    }

    public function edit(RoomRank $room_rank): View
    {
        return view('admin.room-ranks.edit', ['rank' => $room_rank]);
    }

    public function update(Request $request, RoomRank $room_rank): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:80', 'regex:/^[a-z0-9\-]+$/', Rule::unique('room_ranks', 'slug')->ignore($room_rank->id)],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        $room_rank->update($data);

        return redirect()->route('admin.room-ranks.index')->with('status', __('Room rank updated.'));
    }

    public function destroy(RoomRank $room_rank): RedirectResponse
    {
        if ($room_rank->rooms()->exists()) {
            return back()->with('error', __('Detach or reassign rooms before deleting this rank.'));
        }
        $room_rank->delete();

        return redirect()->route('admin.room-ranks.index')->with('status', __('Room rank deleted.'));
    }
}
