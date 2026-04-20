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

        $permissionDefs = [
            ['name' => 'Manage roles', 'slug' => 'manage-roles', 'description' => 'Create, edit, and delete roles; assign permissions to roles.'],
            ['name' => 'Manage permissions', 'slug' => 'manage-permissions', 'description' => 'Create, edit, and delete permissions.'],
            ['name' => 'Manage users', 'slug' => 'manage-users', 'description' => 'Create, edit, and delete system users.'],
            ['name' => 'View reports', 'slug' => 'view-reports', 'description' => 'Access reporting (placeholder for future modules).'],
        ];

        foreach ($permissionDefs as $def) {
            Permission::query()->create($def);
        }

        $superAdmin = Role::query()->create([
            'name' => 'Super admin',
            'slug' => Role::SUPER_ADMIN_SLUG,
            'is_system' => true,
        ]);

        $guest = Role::query()->create([
            'name' => 'Guest',
            'slug' => Role::GUEST_SLUG,
            'is_system' => true,
        ]);

        $superAdmin->permissions()->sync(Permission::query()->pluck('id'));

        User::query()->create([
            'name' => 'Super Admin',
            'email' => 'admin@hotel.test',
            'password' => Hash::make('password'),
            'role_id' => $superAdmin->id,
            'email_verified_at' => now(),
        ]);

        $this->call(AssignSuperAdminByEmailSeeder::class);
        $this->call(RoomRankSeeder::class);
        $this->call(GuestPortalUserSeeder::class);
        $this->call(ExtendedPermissionsSeeder::class);
        $this->call(StaffRolesSeeder::class);
        $this->call(RoomServiceMenuSeeder::class);
    }
}
