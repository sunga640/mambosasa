<?php
// movedata.php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Unganisha na SQLite (Source)
config(['database.connections.sqlite_source' => [
    'driver' => 'sqlite',
    'database' => database_path('database.sqlite'),
]]);

$sqlite = DB::connection('sqlite_source');
$mysql = DB::connection('mysql');

// 2. Orodha ya tables (Hakikisha mpangilio ni mzuri, lakini tutazima ulinzi wa keys)
$tables = [
    'users', 'hotel_branches', 'rooms', 'bookings', 'booking_methods',
    'invoices', 'room_maintenances', 'contact_messages', 'daily_revenues',
    'activity_logs', 'dashboard_notifications', 'restaurant_menu_items',
    'room_service_orders', 'room_service_order_items', 'system_settings'
];

echo "--- Anza kuhamisha data ---\n";

// Zima Foreign Key Checks kwenye MySQL ili kuruhusu truncate na insert
$mysql->statement('SET FOREIGN_KEY_CHECKS=0;');

foreach ($tables as $table) {
    echo "Table: $table... ";

    // Angalia kama table ipo SQLite
    try {
        $rows = $sqlite->table($table)->get();
    } catch (\Exception $e) {
        echo "Haipo SQLite, ruka.\n";
        continue;
    }

    if ($rows->count() > 0) {
        // Safisha MySQL kwanza
        $mysql->table($table)->truncate();

        // Ingiza data kwenye MySQL
        foreach ($rows as $row) {
            // Tunageuza object kuwa array
            $mysql->table($table)->insert((array)$row);
        }
        echo "Sawa (" . $rows->count() . " rows)\n";
    } else {
        echo "Haina data, imeruka.\n";
    }
}

// Washa tena Foreign Key Checks
$mysql->statement('SET FOREIGN_KEY_CHECKS=1;');

echo "--- Kazi imekamilika kikamilifu! ---\n";
