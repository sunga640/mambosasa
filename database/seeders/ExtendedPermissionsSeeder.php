<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Support\PermissionCatalog;
use Illuminate\Database\Seeder;

class ExtendedPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $defs = PermissionCatalog::definitions();

        foreach ($defs as $def) {
            Permission::query()->updateOrCreate(
                ['slug' => $def['slug']],
                ['name' => $def['name'], 'description' => $def['description']]
            );
        }

        $super = Role::query()->where('slug', Role::SUPER_ADMIN_SLUG)->first();
        if ($super) {
            $super->permissions()->syncWithoutDetaching(Permission::query()->pluck('id'));
        }
    }
}
