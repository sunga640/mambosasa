<?php

namespace App\Repositories\Contracts;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

interface RoleRepositoryInterface
{
    public function allOrdered(): Collection;

    public function find(int $id): ?Role;

    public function findOrFail(int $id): Role;

    public function create(array $data): Role;

    public function update(Role $role, array $data): Role;

    public function delete(Role $role): void;
}
