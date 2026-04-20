<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Repositories\Contracts\AdminUserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminUserService
{
    public function __construct(
        private AdminUserRepositoryInterface $users,
    ) {}

    public function create(string $name, string $email, string $password, ?int $roleId, ?int $hotelBranchId = null): User
    {
        return $this->users->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role_id' => $roleId,
            'hotel_branch_id' => $hotelBranchId,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    public function update(User $user, string $name, string $email, ?int $roleId, ?string $password, ?int $hotelBranchId = null): User
    {
        $data = [
            'name' => $name,
            'email' => $email,
            'role_id' => $roleId,
            'hotel_branch_id' => $hotelBranchId,
        ];

        if ($password) {
            $data['password'] = Hash::make($password);
        }

        return $this->users->update($user, $data);
    }

    public function delete(User $user): void
    {
        if ($user->id === auth()->id()) {
            throw ValidationException::withMessages([
                'user' => __('You cannot delete your own account.'),
            ]);
        }

        $this->users->delete($user);
    }
}
