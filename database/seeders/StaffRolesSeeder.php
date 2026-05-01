<?php

namespace Database\Seeders;

use App\Models\BookingMethod;
use App\Models\Permission;
use App\Models\Role;
use App\Support\PermissionCatalog;
use Illuminate\Database\Seeder;

class StaffRolesSeeder extends Seeder
{
    public function run(): void
    {
        BookingMethod::query()->updateOrCreate(
            ['slug' => 'cash'],
            [
                'name' => 'Cash (reception)',
                'is_active' => true,
                'sort_order' => 5,
                'visibility' => BookingMethod::VIS_INTERNAL,
            ]
        );

        $perms = Permission::query()->pluck('id');

        $guest = Role::query()->updateOrCreate(
            ['slug' => Role::GUEST_SLUG],
            ['name' => 'Guest', 'is_system' => true]
        );
        $reception = Role::query()->updateOrCreate(
            ['slug' => Role::RECEPTION_SLUG],
            ['name' => 'Receptionist', 'is_system' => true]
        );
        $director = Role::query()->updateOrCreate(
            ['slug' => Role::DIRECTOR_SLUG],
            ['name' => 'Director', 'is_system' => true]
        );
        $manager = Role::query()->updateOrCreate(
            ['slug' => Role::MANAGER_SLUG],
            ['name' => 'Manager', 'is_system' => true]
        );
        $kitchen = Role::query()->updateOrCreate(
            ['slug' => Role::KITCHEN_SLUG],
            ['name' => 'Kitchen', 'is_system' => true]
        );

        $defaultRoles = PermissionCatalog::defaultRolePermissionSlugs();

        $reception->permissions()->syncWithoutDetaching(
            Permission::query()->whereIn('slug', $defaultRoles[Role::RECEPTION_SLUG] ?? [])->pluck('id')
        );
        $director->permissions()->syncWithoutDetaching(
            Permission::query()->whereIn('slug', $defaultRoles[Role::DIRECTOR_SLUG] ?? [])->pluck('id')
        );
        $manager->permissions()->syncWithoutDetaching(
            Permission::query()->whereIn('slug', $defaultRoles[Role::MANAGER_SLUG] ?? [])->pluck('id')
        );
        $kitchen->permissions()->syncWithoutDetaching(
            Permission::query()->whereIn('slug', $defaultRoles[Role::KITCHEN_SLUG] ?? [])->pluck('id')
        );
        $guest->permissions()->sync([]);

        $super = Role::query()->where('slug', Role::SUPER_ADMIN_SLUG)->first();
        if ($super) {
            $super->update(['is_system' => true]);
            $super->permissions()->syncWithoutDetaching($perms);
        }
    }
}
