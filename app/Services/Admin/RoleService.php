<?php

namespace App\Services\Admin;

use App\Models\Role;
use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RoleService
{
    public function __construct(
        private RoleRepositoryInterface $roles,
    ) {}

    public function create(string $name, ?string $slug, array $permissionIds): Role
    {
        $slug = $slug ? Str::slug($slug) : Str::slug($name);

        return DB::transaction(function () use ($name, $slug, $permissionIds) {
            $role = $this->roles->create([
                'name' => $name,
                'slug' => $this->uniqueSlug($slug),
                'is_system' => false,
            ]);
            $role->permissions()->sync($permissionIds);

            return $role->load('permissions');
        });
    }

    public function update(Role $role, string $name, ?string $slug, array $permissionIds): Role
    {
        if ($role->is_system) {
            $slugFinal = $role->slug;
        } else {
            $slugFinal = $slug ? Str::slug($slug) : Str::slug($name);
            if ($slugFinal !== $role->slug) {
                $slugFinal = $this->uniqueSlug($slugFinal, $role->id);
            }
        }

        return DB::transaction(function () use ($role, $name, $slugFinal, $permissionIds) {
            $this->roles->update($role, [
                'name' => $name,
                'slug' => $slugFinal,
            ]);
            $role->permissions()->sync($permissionIds);

            return $role->fresh()->load('permissions');
        });
    }

    public function delete(Role $role): void
    {
        if ($role->is_system) {
            throw ValidationException::withMessages([
                'role' => __('System roles cannot be deleted.'),
            ]);
        }

        if ($role->users()->exists()) {
            throw ValidationException::withMessages([
                'role' => __('Cannot delete a role that is assigned to users.'),
            ]);
        }

        DB::transaction(function () use ($role) {
            $role->permissions()->detach();
            $this->roles->delete($role);
        });
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug;
        $i = 1;

        while (Role::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
