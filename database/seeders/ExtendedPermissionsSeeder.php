<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class ExtendedPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $defs = [
            ['name' => 'Manage bookings', 'slug' => 'manage-bookings', 'description' => 'View and update booking status and related records.'],
            ['name' => 'Delete bookings', 'slug' => 'delete-bookings', 'description' => 'Remove non-confirmed bookings and related invoice rows.'],
            ['name' => 'Cancel pending bookings', 'slug' => 'cancel-pending-bookings', 'description' => 'Mark pending-payment bookings as cancelled from operations screens.'],
            ['name' => 'Resend payment reminders', 'slug' => 'resend-payment-reminders', 'description' => 'Trigger payment reminder email/SMS for pending bookings.'],
            ['name' => 'Manage customers', 'slug' => 'manage-customers', 'description' => 'View, deactivate, or remove guest customer profiles.'],
            ['name' => 'Manage maintenance', 'slug' => 'manage-maintenance', 'description' => 'Create and edit room maintenance logs.'],
            ['name' => 'Manage invoices', 'slug' => 'manage-invoices', 'description' => 'Resend invoices and access billing links.'],
            ['name' => 'Manage payment methods', 'slug' => 'manage-payment-methods', 'description' => 'Create, edit, activate, and disable booking payment methods.'],
            ['name' => 'Manage system settings', 'slug' => 'manage-system-settings', 'description' => 'Update site, booking, and mail delivery settings.'],
            ['name' => 'Manage branches', 'slug' => 'manage-branches', 'description' => 'Create and update hotel branches.'],
            ['name' => 'Manage rooms', 'slug' => 'manage-rooms', 'description' => 'Create and update rooms and room states.'],
            ['name' => 'Manage room categories', 'slug' => 'manage-room-categories', 'description' => 'Create and update room categories/types.'],
            ['name' => 'Manage media library', 'slug' => 'manage-media-library', 'description' => 'Upload and maintain shared media assets.'],
            ['name' => 'Manage contacts', 'slug' => 'manage-contacts', 'description' => 'View and manage contact form submissions.'],
            ['name' => 'Manage newsletters', 'slug' => 'manage-newsletters', 'description' => 'View and manage newsletter subscribers.'],
            ['name' => 'Manage dashboard analytics', 'slug' => 'view-dashboard-analytics', 'description' => 'Access visual analytics and trends on dashboards.'],
            ['name' => 'Export reports', 'slug' => 'export-reports', 'description' => 'Download CSV reports and full data exports.'],
            ['name' => 'Reception panel', 'slug' => 'access-reception-panel', 'description' => 'Open the reception dashboard and operational screens.'],
            ['name' => 'Reception reports', 'slug' => 'view-reception-reports', 'description' => 'View reception payment summaries and date-filtered reports.'],
            ['name' => 'Manage room service (reception)', 'slug' => 'manage-room-service-reception', 'description' => 'View and update in-house room service orders.'],
            ['name' => 'Manage roles', 'slug' => 'manage-roles', 'description' => 'Create, edit, and delete custom roles (non-system).'],
            ['name' => 'Manage permissions catalog', 'slug' => 'manage-permissions', 'description' => 'View and describe permission definitions.'],
            ['name' => 'Manage staff users', 'slug' => 'manage-staff-users', 'description' => 'Create and update user accounts for staff.'],
            ['name' => 'View activity log', 'slug' => 'view-activity-log', 'description' => 'Read system activity and audit-style events.'],
            ['name' => 'Manage hotel services catalog', 'slug' => 'manage-hotel-services', 'description' => 'Maintain add-on services and catalog pricing.'],
            ['name' => 'Manage properties directory', 'slug' => 'manage-properties-directory', 'description' => 'Edit marketing/property directory content.'],
            ['name' => 'Manage notifications', 'slug' => 'manage-dashboard-notifications', 'description' => 'Resolve or acknowledge operational dashboard notifications.'],
            ['name' => 'Branch scope override', 'slug' => 'switch-branch-scope', 'description' => 'Switch admin analytics scope between branches.'],
        ];

        foreach ($defs as $def) {
            Permission::query()->firstOrCreate(
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
