<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssignSuperAdminByEmailSeeder extends Seeder
{
    /**
     * Assign super admin role to every user whose email is listed in config('hotel.super_admin_emails').
     */
    public function run(): void
    {
        $role = Role::query()->where('slug', Role::SUPER_ADMIN_SLUG)->first();

        if (! $role) {
            $this->command?->warn('Super admin role not found. Run DatabaseSeeder first.');

            return;
        }

        foreach (config('hotel.super_admin_emails', []) as $email) {
            if ($email === '') {
                continue;
            }
            $updated = User::query()->whereRaw('LOWER(email) = ?', [$email])->update(['role_id' => $role->id]);
            if ($updated && isset($this->command)) {
                $this->command->info("Super admin role assigned to: {$email}");
            }
        }
    }
}
