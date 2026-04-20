<?php

namespace App\Repositories\Eloquent;

use App\Models\Permission;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PermissionRepository implements PermissionRepositoryInterface
{
    public function allOrdered(): Collection
    {
        return Permission::query()->orderBy('name')->get();
    }

    public function findOrFail(int $id): Permission
    {
        return Permission::query()->findOrFail($id);
    }

    public function create(array $data): Permission
    {
        return Permission::query()->create($data);
    }

    public function update(Permission $permission, array $data): Permission
    {
        $permission->update($data);

        return $permission->fresh();
    }

    public function delete(Permission $permission): void
    {
        $permission->delete();
    }
}
