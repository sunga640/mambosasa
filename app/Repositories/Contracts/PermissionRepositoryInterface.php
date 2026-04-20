<?php

namespace App\Repositories\Contracts;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

interface PermissionRepositoryInterface
{
    public function allOrdered(): Collection;

    public function findOrFail(int $id): Permission;

    public function create(array $data): Permission;

    public function update(Permission $permission, array $data): Permission;

    public function delete(Permission $permission): void;
}
