<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\Role;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Services\Admin\RoleService;
use App\Support\PermissionCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct(
        private RoleRepositoryInterface $roles,
        private PermissionRepositoryInterface $permissions,
        private RoleService $roleService,
    ) {}

    public function index(): View
    {
        return view('admin.roles.index', [
            'roles' => Role::query()->with('permissions')->orderBy('name')->paginate(7),
        ]);
    }

    public function create(): View
    {
        $permissions = $this->permissions->allOrdered();

        return view('admin.roles.create', [
            'permissions' => $permissions,
            'permissionGroups' => PermissionCatalog::groupedCollection($permissions),
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $this->roleService->create(
            $request->validated('name'),
            $request->validated('slug'),
            $request->validated('permission_ids', []),
        );

        return redirect()->route('admin.roles.index')->with('status', __('Role created.'));
    }

    public function edit(Role $role): View
    {
        $permissions = $this->permissions->allOrdered();

        return view('admin.roles.edit', [
            'role' => $role->load('permissions'),
            'permissions' => $permissions,
            'permissionGroups' => PermissionCatalog::groupedCollection($permissions),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $this->roleService->update(
            $role,
            $request->validated('name'),
            $request->validated('slug'),
            $request->validated('permission_ids', []),
        );

        return redirect()->route('admin.roles.index')->with('status', __('Role updated.'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->roleService->delete($role);

        return redirect()->route('admin.roles.index')->with('status', __('Role deleted.'));
    }
}
