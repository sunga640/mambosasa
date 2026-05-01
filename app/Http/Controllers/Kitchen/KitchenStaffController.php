<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RoomServiceOrder;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class KitchenStaffController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $staff = User::query()
            ->with(['role', 'assignedRoomServiceOrders'])
            ->whereHas('role', function ($query): void {
                $query->where('slug', Role::KITCHEN_SLUG)
                    ->orWhere('context', 'kitchen');
            })
            ->where(function ($query) use ($user): void {
                $query->where('created_by_user_id', $user?->id)
                    ->orWhere('id', $user?->id);
            })
            ->orderBy('name')
            ->get();

        return view('kitchen.staff.index', [
            'staff' => $staff,
            'roles' => $this->availableRoles(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $roleIds = $this->availableRoles()->pluck('id')->all();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role_id' => ['required', 'integer', 'in:'.implode(',', $roleIds)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => (int) $data['role_id'],
            'hotel_branch_id' => auth()->user()?->hotel_branch_id,
            'created_by_user_id' => auth()->id(),
            'is_active' => $request->boolean('is_active', true),
            'email_verified_at' => now(),
        ]);

        return back()->with('status', __('Kitchen staff account created.'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless((int) $user->created_by_user_id === (int) auth()->id(), 404);

        $roleIds = $this->availableRoles()->pluck('id')->all();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'role_id' => ['required', 'integer', 'in:'.implode(',', $roleIds)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $updates = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role_id' => (int) $data['role_id'],
            'is_active' => $request->boolean('is_active'),
        ];

        if (! empty($data['password'])) {
            $updates['password'] = Hash::make($data['password']);
        }

        $user->update($updates);

        return back()->with('status', __('Kitchen staff account updated.'));
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless((int) $user->created_by_user_id === (int) auth()->id(), 404);
        abort_if((int) $user->id === (int) auth()->id(), 422, __('You cannot delete your own kitchen account from this page.'));

        RoomServiceOrder::query()
            ->where('assigned_to_user_id', $user->id)
            ->update([
                'assigned_to_user_id' => null,
                'assigned_at' => null,
            ]);

        $user->delete();

        return back()->with('status', __('Kitchen staff account deleted.'));
    }

    private function availableRoles()
    {
        return Role::query()
            ->where(function ($query): void {
                $query->where('slug', Role::KITCHEN_SLUG)
                    ->orWhere(function ($inner): void {
                        $inner->where('context', 'kitchen')
                            ->where('created_by_user_id', auth()->id());
                    });
            })
            ->orderBy('is_system', 'desc')
            ->orderBy('name')
            ->get();
    }
}
