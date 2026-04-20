<?php

namespace App\Services\Admin;

use App\Models\Permission;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermissionService
{
    public function __construct(
        private PermissionRepositoryInterface $permissions,
    ) {}

    public function create(string $name, ?string $slug, ?string $description): Permission
    {
        $slug = $slug ? Str::slug($slug) : Str::slug($name);

        return $this->permissions->create([
            'name' => $name,
            'slug' => $this->uniqueSlug($slug),
            'description' => $description,
        ]);
    }

    public function update(Permission $permission, string $name, ?string $slug, ?string $description): Permission
    {
        $slugFinal = $slug ? Str::slug($slug) : Str::slug($name);
        if ($slugFinal !== $permission->slug) {
            $slugFinal = $this->uniqueSlug($slugFinal, $permission->id);
        }

        return $this->permissions->update($permission, [
            'name' => $name,
            'slug' => $slugFinal,
            'description' => $description,
        ]);
    }

    public function delete(Permission $permission): void
    {
        DB::transaction(function () use ($permission) {
            $permission->roles()->detach();
            $this->permissions->delete($permission);
        });
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug;
        $i = 1;

        while (Permission::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
