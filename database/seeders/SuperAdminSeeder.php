<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // hakikisha role ipo
        $role = Role::firstOrCreate(
            ['slug' => 'super_admin'],
            ['name' => 'Super Admin']
        );

        // create/update user
        User::updateOrCreate(
            ['email' => 'sungamourice@gmail.com'],
            [
                'name' => 'mourice sunga',
                'password' => Hash::make('password'),
                'role_id' => $role->id,
            ]
        );
    }
}
