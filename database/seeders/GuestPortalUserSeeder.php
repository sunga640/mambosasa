<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GuestPortalUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('hotel.guest_portal_user_email');
        $guestRole = Role::query()->where('slug', Role::GUEST_SLUG)->first();
        if (! $guestRole) {
            return;
        }

        User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Guest portal (system)',
                'password' => Hash::make(str()->random(48)),
                'role_id' => $guestRole->id,
                'is_active' => false,
            ]
        );
    }
}
