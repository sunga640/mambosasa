<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        SystemSetting::query()->firstOrCreate(
            [],
            [
                'company_name' => 'Hotel & Spa Swiss Resort',
                'address_line' => 'PO Box 16122 Collins Street West, Victoria 8007 Australia',
                'phone' => '+41-96567-7854',
                'email' => 'info@swiss-resort.com',
                'copyright_text' => 'Copyright © '.date('Y').' '.config('app.name'),
            ]
        );
        SystemSetting::forgetCache();

        $this->call(BookingMethodSeeder::class);

        $superAdmin = Role::query()->firstOrCreate([
            'slug' => Role::SUPER_ADMIN_SLUG,
        ], [
            'name' => 'Super admin',
            'is_system' => true,
        ]);

        Role::query()->firstOrCreate([
            'slug' => Role::GUEST_SLUG,
        ], [
            'name' => 'Guest',
            'is_system' => true,
        ]);

        Role::query()->firstOrCreate([
            'slug' => Role::MANAGER_SLUG,
        ], [
            'name' => 'Manager',
            'is_system' => true,
        ]);

        $this->call(ExtendedPermissionsSeeder::class);
        $superAdmin->permissions()->syncWithoutDetaching(Permission::query()->pluck('id'));

        User::query()->firstOrCreate([
            'email' => 'admin@hotel.test',
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
            'role_id' => $superAdmin->id,
            'email_verified_at' => now(),
        ]);

        $this->call(AssignSuperAdminByEmailSeeder::class);
        $this->call(RoomRankSeeder::class);
        $this->call(GuestPortalUserSeeder::class);
        $this->call(StaffRolesSeeder::class);
        $this->call(RoomServiceMenuSeeder::class);
    }
}
