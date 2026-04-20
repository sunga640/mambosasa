<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\AdminUserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminUserRepository implements AdminUserRepositoryInterface
{
    public function paginate(int $perPage = 7): LengthAwarePaginator
    {
        return User::query()
            ->with(['role', 'hotelBranch'])
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function findOrFail(int $id): User
    {
        return User::query()->with('role.permissions')->findOrFail($id);
    }

    public function create(array $data): User
    {
        return User::query()->create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->fresh();
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
