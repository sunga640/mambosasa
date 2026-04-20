<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminUserRequest;
use App\Http\Requests\Admin\UpdateAdminUserRequest;
use App\Models\HotelBranch;
use App\Models\User;
use App\Repositories\Contracts\AdminUserRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Services\Admin\AdminUserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private AdminUserRepositoryInterface $users,
        private RoleRepositoryInterface $roles,
        private AdminUserService $userService,
    ) {}

    public function index(): View
    {
        return view('admin.users.index', [
            'users' => $this->users->paginate(7),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => $this->roles->allOrdered(),
            'branches' => HotelBranch::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreAdminUserRequest $request): RedirectResponse
    {
        $this->userService->create(
            $request->validated('name'),
            $request->validated('email'),
            $request->validated('password'),
            $request->validated('role_id'),
            $request->validated('hotel_branch_id'),
        );

        return redirect()->route('admin.users.index')->with('status', __('User created.'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user->load('hotelBranch'),
            'roles' => $this->roles->allOrdered(),
            'branches' => HotelBranch::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateAdminUserRequest $request, User $user): RedirectResponse
    {
        $this->userService->update(
            $user,
            $request->validated('name'),
            $request->validated('email'),
            $request->validated('role_id'),
            $request->validated('password'),
            $request->validated('hotel_branch_id'),
        );

        return redirect()->route('admin.users.index')->with('status', __('User updated.'));
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->userService->delete($user);

        return redirect()->route('admin.users.index')->with('status', __('User deleted.'));
    }

    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => __('You cannot deactivate your own account.')]);
        }

        $user->update(['is_active' => ! ($user->is_active ?? true)]);

        $active = $user->fresh()->is_active ?? true;

        return back()->with('status', $active ? __('User activated.') : __('User deactivated.'));
    }
}
