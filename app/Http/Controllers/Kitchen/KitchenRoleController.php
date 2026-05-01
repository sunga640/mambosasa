<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Support\PermissionCatalog;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KitchenRoleController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $permissions = Permission::query()
            ->whereIn('slug', $this->allowedPermissionSlugs())
            ->orderBy('name')
            ->get();

        $roles = Role::query()
            ->with('permissions')
            ->where(function ($query) use ($user): void {
                $query->where('slug', Role::KITCHEN_SLUG)
                    ->orWhere(function ($inner) use ($user): void {
                        $inner->where('context', 'kitchen')
                            ->where('created_by_user_id', $user?->id);
                    });
            })
            ->orderBy('is_system', 'desc')
            ->orderBy('name')
            ->paginate(6)
            ->withQueryString();

        return view('kitchen.roles.index', [
            'roles' => $roles,
            'permissions' => $permissions,
            'permissionGroups' => PermissionCatalog::groupedCollection($permissions),
            'allowedPermissionSlugs' => $this->allowedPermissionSlugs(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $permissionIds = Permission::query()
            ->whereIn('slug', $this->allowedPermissionSlugs())
            ->pluck('id')
            ->all();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'permission_ids' => ['array'],
            'permission_ids.*' => ['integer', 'in:'.implode(',', $permissionIds)],
        ]);

        $role = Role::query()->create([
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['slug'] ?: $data['name']),
            'is_system' => false,
            'context' => 'kitchen',
            'created_by_user_id' => auth()->id(),
            'hotel_branch_id' => auth()->user()?->hotel_branch_id,
        ]);

        $selected = collect($data['permission_ids'] ?? [])->map(fn ($id) => (int) $id)->all();
        $accessId = Permission::query()->where('slug', 'access-kitchen-panel')->value('id');
        if ($accessId && ! in_array((int) $accessId, $selected, true)) {
            $selected[] = (int) $accessId;
        }

        $role->permissions()->sync($selected);

        return back()->with('status', __('Kitchen role created.'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        abort_unless($role->id && ($role->slug === Role::KITCHEN_SLUG || ($role->context === 'kitchen' && (int) $role->created_by_user_id === (int) auth()->id())), 404);

        $permissionIds = Permission::query()
            ->whereIn('slug', $this->allowedPermissionSlugs())
            ->pluck('id')
            ->all();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'permission_ids' => ['array'],
            'permission_ids.*' => ['integer', 'in:'.implode(',', $permissionIds)],
        ]);

        $role->update([
            'name' => $data['name'],
            'slug' => $role->is_system ? $role->slug : $this->uniqueSlug($data['slug'] ?: $data['name'], $role->id),
        ]);

        $selected = collect($data['permission_ids'] ?? [])->map(fn ($id) => (int) $id)->all();
        $accessId = Permission::query()->where('slug', 'access-kitchen-panel')->value('id');
        if ($accessId && ! in_array((int) $accessId, $selected, true)) {
            $selected[] = (int) $accessId;
        }
        $role->permissions()->sync($selected);

        return back()->with('status', __('Kitchen role updated.'));
    }

    public function updateMatrix(Request $request): RedirectResponse
    {
        $roles = $this->editableRoles();
        $roleIds = $roles->pluck('id')->map(fn ($id) => (string) $id)->all();
        $permissionIds = Permission::query()
            ->whereIn('slug', $this->allowedPermissionSlugs())
            ->pluck('id')
            ->all();

        $data = $request->validate([
            'roles' => ['required', 'array'],
            'roles.*.id' => ['required', 'in:'.implode(',', $roleIds)],
            'roles.*.name' => ['required', 'string', 'max:255'],
            'roles.*.slug' => ['nullable', 'string', 'max:255'],
            'roles.*.permission_ids' => ['array'],
            'roles.*.permission_ids.*' => ['integer', 'in:'.implode(',', $permissionIds)],
        ]);

        $accessId = Permission::query()->where('slug', 'access-kitchen-panel')->value('id');

        foreach ($data['roles'] as $payload) {
            /** @var Role $role */
            $role = $roles->firstWhere('id', (int) $payload['id']);
            if (! $role) {
                continue;
            }

            $role->update([
                'name' => $payload['name'],
                'slug' => $role->is_system ? $role->slug : $this->uniqueSlug($payload['slug'] ?: $payload['name'], $role->id),
            ]);

            $selected = collect($payload['permission_ids'] ?? [])->map(fn ($id) => (int) $id)->all();
            if ($accessId && ! in_array((int) $accessId, $selected, true)) {
                $selected[] = (int) $accessId;
            }

            $role->permissions()->sync($selected);
        }

        return back()->with('status', __('Kitchen roles matrix updated.'));
    }

    private function allowedPermissionSlugs(): array
    {
        return [
            'access-kitchen-panel',
            'manage-kitchen-orders',
            'assign-kitchen-orders',
            'manage-kitchen-menu',
            'manage-kitchen-staff',
            'manage-kitchen-roles',
            'generate-kitchen-qr',
            'view-kitchen-reports',
            'manage-media-library',
        ];
    }

    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $slug = Str::slug($value);
        $base = $slug;
        $counter = 1;

        while (Role::query()
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function editableRoles(): Collection
    {
        $user = auth()->user();

        return Role::query()
            ->with('permissions')
            ->where(function ($query) use ($user): void {
                $query->where('slug', Role::KITCHEN_SLUG)
                    ->orWhere(function ($inner) use ($user): void {
                        $inner->where('context', 'kitchen')
                            ->where('created_by_user_id', $user?->id);
                    });
            })
            ->get();
    }
}
