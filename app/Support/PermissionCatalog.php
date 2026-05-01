<?php

namespace App\Support;

use Illuminate\Support\Collection;

final class PermissionCatalog
{
    public static function groups(): array
    {
        return [
            'reservations' => [
                'label' => 'Bookings & Front Desk',
                'description' => 'Reservations, pending payments, customer handling, invoices, and reception desk actions.',
                'permissions' => [
                    ['slug' => 'manage-bookings', 'name' => 'Manage bookings', 'description' => 'View, create, update, and confirm booking records.'],
                    ['slug' => 'delete-bookings', 'name' => 'Delete bookings', 'description' => 'Delete bookings from operational tables when deletion is allowed.'],
                    ['slug' => 'cancel-pending-bookings', 'name' => 'Cancel pending bookings', 'description' => 'Cancel unpaid or pending bookings from operations screens.'],
                    ['slug' => 'resend-payment-reminders', 'name' => 'Resend payment reminders', 'description' => 'Trigger reminder delivery for pending booking payments.'],
                    ['slug' => 'manage-customers', 'name' => 'Manage customers', 'description' => 'View, edit, deactivate, and remove guest customer records.'],
                    ['slug' => 'manage-invoices', 'name' => 'Manage invoices', 'description' => 'Resend invoices and handle booking billing actions.'],
                    ['slug' => 'manage-payment-methods', 'name' => 'Manage payment methods', 'description' => 'Create, update, enable, and disable payment methods.'],
                    ['slug' => 'access-reception-panel', 'name' => 'Access reception panel', 'description' => 'Open the reception dashboard and front desk workspace.'],
                ],
            ],
            'property' => [
                'label' => 'Property & Inventory',
                'description' => 'Hotel branches, rooms, room categories, and maintenance operations.',
                'permissions' => [
                    ['slug' => 'manage-branches', 'name' => 'Manage branches', 'description' => 'Create and update hotel branches and branch profile details.'],
                    ['slug' => 'manage-properties-directory', 'name' => 'Manage properties directory', 'description' => 'Edit public-facing properties and branch marketing content.'],
                    ['slug' => 'manage-rooms', 'name' => 'Manage rooms', 'description' => 'Create, update, and change room status information.'],
                    ['slug' => 'manage-room-categories', 'name' => 'Manage room categories', 'description' => 'Manage room types, categories, and availability groupings.'],
                    ['slug' => 'manage-maintenance', 'name' => 'Manage maintenance', 'description' => 'Log, assign, and resolve room maintenance items.'],
                ],
            ],
            'services' => [
                'label' => 'Services & Content',
                'description' => 'Hotel services, room service, media assets, contact inbox, and newsletter lists.',
                'permissions' => [
                    ['slug' => 'manage-hotel-services', 'name' => 'Manage hotel services', 'description' => 'Maintain hotel services catalog and service pricing.'],
                    ['slug' => 'manage-room-service-reception', 'name' => 'Manage room service', 'description' => 'Handle in-house room service orders and related front desk actions.'],
                    ['slug' => 'access-kitchen-panel', 'name' => 'Access kitchen panel', 'description' => 'Open the dedicated kitchen dashboard and workspace.'],
                    ['slug' => 'manage-kitchen-orders', 'name' => 'Manage kitchen orders', 'description' => 'Review, update, and complete kitchen orders from room scans and in-stay service requests.'],
                    ['slug' => 'assign-kitchen-orders', 'name' => 'Assign kitchen orders', 'description' => 'Assign room-service orders to kitchen staff and monitor who is responsible for each task.'],
                    ['slug' => 'manage-kitchen-menu', 'name' => 'Manage kitchen menu', 'description' => 'Create, update, activate, and organize kitchen menu items and preparation times.'],
                    ['slug' => 'manage-kitchen-staff', 'name' => 'Manage kitchen staff', 'description' => 'Create, update, and supervise kitchen staff accounts inside the kitchen workspace.'],
                    ['slug' => 'manage-kitchen-roles', 'name' => 'Manage kitchen roles', 'description' => 'Create role templates for kitchen staff and control which kitchen permissions they receive.'],
                    ['slug' => 'generate-kitchen-qr', 'name' => 'Generate kitchen QR', 'description' => 'Generate and print QR access codes for room menu ordering.' ],
                    ['slug' => 'view-kitchen-reports', 'name' => 'View kitchen reports', 'description' => 'Open kitchen KPIs, throughput reports, and order analytics.' ],
                    ['slug' => 'manage-media-library', 'name' => 'Manage media library', 'description' => 'Upload, organize, and delete media assets used across the system.'],
                    ['slug' => 'manage-contacts', 'name' => 'Manage contacts', 'description' => 'Read, reply to, and remove contact form submissions.'],
                    ['slug' => 'manage-newsletters', 'name' => 'Manage newsletters', 'description' => 'View and manage newsletter subscribers.'],
                ],
            ],
            'reports' => [
                'label' => 'Reports & Analytics',
                'description' => 'Dashboards, exports, notifications, activity, and branch-level analytics tools.',
                'permissions' => [
                    ['slug' => 'view-dashboard-analytics', 'name' => 'View dashboard analytics', 'description' => 'Open dashboard charts, KPIs, and analytics blocks.'],
                    ['slug' => 'view-reports', 'name' => 'View reports', 'description' => 'Open administrative reports and summary pages.'],
                    ['slug' => 'view-reception-reports', 'name' => 'View reception reports', 'description' => 'Open front desk reports and payment summaries.'],
                    ['slug' => 'export-reports', 'name' => 'Export reports', 'description' => 'Download report exports and generated report files.'],
                    ['slug' => 'manage-dashboard-notifications', 'name' => 'Manage dashboard notifications', 'description' => 'Read, acknowledge, and resolve dashboard notifications.'],
                    ['slug' => 'view-activity-log', 'name' => 'View activity log', 'description' => 'Read audit-style activity and tracking logs.'],
                    ['slug' => 'switch-branch-scope', 'name' => 'Switch branch scope', 'description' => 'Change dashboard and report scope between hotel branches.'],
                ],
            ],
            'access' => [
                'label' => 'Access & Settings',
                'description' => 'Roles, permissions, user accounts, and overall system configuration.',
                'permissions' => [
                    ['slug' => 'manage-system-settings', 'name' => 'Manage system settings', 'description' => 'Update site-wide settings, branding, booking, and mail options.'],
                    ['slug' => 'manage-roles', 'name' => 'Manage roles', 'description' => 'Create, update, and delete custom roles.'],
                    ['slug' => 'manage-permissions', 'name' => 'Manage permissions', 'description' => 'Review, create, and edit permission definitions.'],
                    ['slug' => 'manage-users', 'name' => 'Manage users', 'description' => 'Create, update, activate, and manage system users.'],
                    ['slug' => 'manage-staff-users', 'name' => 'Manage staff users', 'description' => 'Manage staff-specific user accounts and assignments.'],
                ],
            ],
        ];
    }

    public static function definitions(): array
    {
        $definitions = [];

        foreach (self::groups() as $groupKey => $group) {
            foreach ($group['permissions'] as $index => $permission) {
                $definitions[] = $permission + [
                    'group' => $groupKey,
                    'group_label' => $group['label'],
                    'group_description' => $group['description'],
                    'sort' => $index,
                ];
            }
        }

        return $definitions;
    }

    public static function definitionsBySlug(): array
    {
        $map = [];

        foreach (self::definitions() as $definition) {
            $map[$definition['slug']] = $definition;
        }

        return $map;
    }

    public static function find(string $slug): ?array
    {
        return self::definitionsBySlug()[self::normalizeSlug($slug)] ?? null;
    }

    public static function normalizeSlug(string $slug): string
    {
        $normalized = trim($slug);

        return self::aliases()[$normalized] ?? $normalized;
    }

    public static function aliases(): array
    {
        return [
            'manage-settings' => 'manage-system-settings',
        ];
    }

    public static function groupedCollection(Collection $permissions): array
    {
        $definitions = self::definitionsBySlug();
        $permissionsBySlug = $permissions->keyBy(fn ($permission) => self::normalizeSlug((string) $permission->slug));
        $groups = [];

        foreach (self::groups() as $groupKey => $group) {
            $items = collect($group['permissions'])
                ->map(fn (array $definition) => $permissionsBySlug->get($definition['slug']))
                ->filter()
                ->values();

            if ($items->isEmpty()) {
                continue;
            }

            $groups[] = [
                'key' => $groupKey,
                'label' => $group['label'],
                'description' => $group['description'],
                'permissions' => $items,
            ];
        }

        $knownSlugs = array_keys($definitions);
        $uncategorized = $permissions->filter(function ($permission) use ($knownSlugs) {
            return ! in_array(self::normalizeSlug((string) $permission->slug), $knownSlugs, true);
        })->sortBy('name')->values();

        if ($uncategorized->isNotEmpty()) {
            $groups[] = [
                'key' => 'uncategorized',
                'label' => 'Other permissions',
                'description' => 'Permissions that are not yet mapped into the catalog.',
                'permissions' => $uncategorized,
            ];
        }

        return $groups;
    }

    public static function receptionPanelPermissionSlugs(): array
    {
        return [
            'manage-bookings',
            'delete-bookings',
            'cancel-pending-bookings',
            'resend-payment-reminders',
            'manage-customers',
            'manage-invoices',
            'manage-payment-methods',
            'manage-rooms',
            'manage-room-categories',
            'manage-maintenance',
            'manage-room-service-reception',
            'manage-hotel-services',
            'view-reception-reports',
            'view-dashboard-analytics',
            'access-reception-panel',
        ];
    }

    public static function adminPanelPermissionSlugs(): array
    {
        return [
            'manage-payment-methods',
            'manage-branches',
            'manage-properties-directory',
            'manage-media-library',
            'manage-contacts',
            'manage-newsletters',
            'view-reports',
            'export-reports',
            'manage-dashboard-notifications',
            'view-activity-log',
            'switch-branch-scope',
            'manage-system-settings',
            'manage-roles',
            'manage-permissions',
            'manage-users',
            'manage-staff-users',
        ];
    }

    public static function kitchenPanelPermissionSlugs(): array
    {
        return [
            'access-kitchen-panel',
            'manage-kitchen-orders',
            'assign-kitchen-orders',
            'manage-kitchen-menu',
            'manage-kitchen-staff',
            'manage-kitchen-roles',
            'generate-kitchen-qr',
            'view-kitchen-reports',
            'manage-media-library',
        ];
    }

    public static function defaultRolePermissionSlugs(): array
    {
        return [
            'reception' => self::receptionPanelPermissionSlugs(),
            'director' => array_values(array_unique(array_merge(
                self::adminPanelPermissionSlugs(),
                self::receptionPanelPermissionSlugs(),
                self::kitchenPanelPermissionSlugs(),
            ))),
            'manager' => array_values(array_unique(array_merge(
                self::adminPanelPermissionSlugs(),
                self::receptionPanelPermissionSlugs(),
                self::kitchenPanelPermissionSlugs()
            ))),
            'kitchen' => self::kitchenPanelPermissionSlugs(),
            'guest' => [],
        ];
    }
}
