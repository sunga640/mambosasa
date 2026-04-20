<?php

namespace App\Repositories\Eloquent;

use App\Models\Role;
use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository implements RoleRepositoryInterface
{
    public function allOrdered(): Collection
    {
        return Role::query()->with('permissions')->orderBy('name')->get();
    }

    public function find(int $id): ?Role
    {
        return Role::query()->find($id);
    }

    public function findOrFail(int $id): Role
    {
        return Role::query()->with('permissions')->findOrFail($id);
    }

    public function create(array $data): Role
    {
        return Role::query()->create($data);
    }

    public function update(Role $role, array $data): Role
    {
        $role->update($data);

        return $role->fresh();
    }

    public function delete(Role $role): void
    {
        $role->delete();
    }
}
