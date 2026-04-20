<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePermissionRequest;
use App\Http\Requests\Admin\UpdatePermissionRequest;
use App\Models\Permission;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Services\Admin\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function __construct(
        private PermissionRepositoryInterface $permissions,
        private PermissionService $permissionService,
    ) {}

    public function index(): View
    {
        return view('admin.permissions.index', [
            'permissions' => Permission::query()->orderBy('name')->paginate(7),
        ]);
    }

    public function create(): View
    {
        return view('admin.permissions.create');
    }

    public function store(StorePermissionRequest $request): RedirectResponse
    {
        $this->permissionService->create(
            $request->validated('name'),
            $request->validated('slug'),
            $request->validated('description'),
        );

        return redirect()->route('admin.permissions.index')->with('status', __('Permission created.'));
    }

    public function edit(Permission $permission): View
    {
        return view('admin.permissions.edit', ['permission' => $permission]);
    }

    public function update(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
    {
        $this->permissionService->update(
            $permission,
            $request->validated('name'),
            $request->validated('slug'),
            $request->validated('description'),
        );

        return redirect()->route('admin.permissions.index')->with('status', __('Permission updated.'));
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $this->permissionService->delete($permission);

        return redirect()->route('admin.permissions.index')->with('status', __('Permission deleted.'));
    }
}
