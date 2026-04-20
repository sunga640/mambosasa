<?php

namespace Database\Seeders;

use App\Models\BookingMethod;
use App\Models\Permission;
use App\Models\Role;
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

        $reception = Role::query()->firstOrCreate(
            ['slug' => Role::RECEPTION_SLUG],
            ['name' => 'Reception', 'is_system' => true]
        );
        $director = Role::query()->firstOrCreate(
            ['slug' => Role::DIRECTOR_SLUG],
            ['name' => 'Director', 'is_system' => true]
        );

        $staffPermSlugs = [
            'manage-bookings',
            'manage-customers',
            'manage-maintenance',
            'manage-invoices',
            'manage-payment-methods',
            'manage-rooms',
            'manage-room-categories',
            'view-dashboard-analytics',
            'view-reports',
            'export-reports',
            'access-reception-panel',
            'view-reception-reports',
            'manage-room-service-reception',
        ];
        $staffPermIds = Permission::query()->whereIn('slug', $staffPermSlugs)->pluck('id');

        $reception->permissions()->syncWithoutDetaching($staffPermIds);
        $director->permissions()->syncWithoutDetaching($staffPermIds);

        $super = Role::query()->where('slug', Role::SUPER_ADMIN_SLUG)->first();
        if ($super) {
            $super->permissions()->syncWithoutDetaching($perms);
        }
    }
}
