<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('context')->nullable()->after('is_system');
            $table->foreignId('created_by_user_id')->nullable()->after('context')->constrained('users')->nullOnDelete();
            $table->foreignId('hotel_branch_id')->nullable()->after('created_by_user_id')->constrained('hotel_branches')->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('created_by_user_id')->nullable()->after('hotel_branch_id')->constrained('users')->nullOnDelete();
        });

        Schema::table('room_service_orders', function (Blueprint $table) {
            $table->foreignId('assigned_to_user_id')->nullable()->after('bill_generated_by_user_id')->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_by_user_id')->nullable()->after('assigned_to_user_id')->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable()->after('assigned_by_user_id');
            $table->timestamp('completed_at')->nullable()->after('assigned_at');
            $table->foreignId('last_status_updated_by_user_id')->nullable()->after('completed_at')->constrained('users')->nullOnDelete();
        });

        $permissions = [
            [
                'name' => 'Assign kitchen orders',
                'slug' => 'assign-kitchen-orders',
                'description' => 'Assign room-service orders to kitchen staff and monitor who is responsible for each task.',
            ],
            [
                'name' => 'Manage kitchen staff',
                'slug' => 'manage-kitchen-staff',
                'description' => 'Create, update, and supervise kitchen staff accounts inside the kitchen workspace.',
            ],
            [
                'name' => 'Manage kitchen roles',
                'slug' => 'manage-kitchen-roles',
                'description' => 'Create role templates for kitchen staff and control which kitchen permissions they receive.',
            ],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $permission['slug']],
                $permission + ['updated_at' => now(), 'created_at' => now()]
            );
        }

        $attachSlugs = ['assign-kitchen-orders', 'manage-kitchen-staff', 'manage-kitchen-roles'];
        $attachIds = DB::table('permissions')->whereIn('slug', $attachSlugs)->pluck('id');

        foreach ([Role::KITCHEN_SLUG, Role::DIRECTOR_SLUG, Role::MANAGER_SLUG, Role::SUPER_ADMIN_SLUG] as $roleSlug) {
            $roleId = DB::table('roles')->where('slug', $roleSlug)->value('id');
            if (! $roleId) {
                continue;
            }

            foreach ($attachIds as $permissionId) {
                DB::table('permission_role')->updateOrInsert(
                    ['role_id' => $roleId, 'permission_id' => $permissionId],
                    ['updated_at' => now(), 'created_at' => now()]
                );
            }
        }
    }

    public function down(): void
    {
        $slugs = ['assign-kitchen-orders', 'manage-kitchen-staff', 'manage-kitchen-roles'];
        $permissionIds = DB::table('permissions')->whereIn('slug', $slugs)->pluck('id');

        if ($permissionIds->isNotEmpty()) {
            DB::table('permission_role')->whereIn('permission_id', $permissionIds)->delete();
            DB::table('permissions')->whereIn('id', $permissionIds)->delete();
        }

        Schema::table('room_service_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('last_status_updated_by_user_id');
            $table->dropColumn(['completed_at', 'assigned_at']);
            $table->dropConstrainedForeignId('assigned_by_user_id');
            $table->dropConstrainedForeignId('assigned_to_user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by_user_id');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hotel_branch_id');
            $table->dropConstrainedForeignId('created_by_user_id');
            $table->dropColumn('context');
        });
    }
};
