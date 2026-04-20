<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AdminUserRepositoryInterface
{
    public function paginate(int $perPage = 7): LengthAwarePaginator;

    public function findOrFail(int $id): User;

    public function create(array $data): User;

    public function update(User $user, array $data): User;

    public function delete(User $user): void;
}
