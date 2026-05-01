<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table): void {
            if (! Schema::hasColumn('system_settings', 'kitchen_weekday_service_start_time')) {
                $table->string('kitchen_weekday_service_start_time', 5)->nullable()->after('booking_checkout_weekend_time');
            }
            if (! Schema::hasColumn('system_settings', 'kitchen_weekday_service_end_time')) {
                $table->string('kitchen_weekday_service_end_time', 5)->nullable()->after('kitchen_weekday_service_start_time');
            }
            if (! Schema::hasColumn('system_settings', 'kitchen_weekend_service_start_time')) {
                $table->string('kitchen_weekend_service_start_time', 5)->nullable()->after('kitchen_weekday_service_end_time');
            }
            if (! Schema::hasColumn('system_settings', 'kitchen_weekend_service_end_time')) {
                $table->string('kitchen_weekend_service_end_time', 5)->nullable()->after('kitchen_weekend_service_start_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table): void {
            $columns = array_values(array_filter([
                Schema::hasColumn('system_settings', 'kitchen_weekday_service_start_time') ? 'kitchen_weekday_service_start_time' : null,
                Schema::hasColumn('system_settings', 'kitchen_weekday_service_end_time') ? 'kitchen_weekday_service_end_time' : null,
                Schema::hasColumn('system_settings', 'kitchen_weekend_service_start_time') ? 'kitchen_weekend_service_start_time' : null,
                Schema::hasColumn('system_settings', 'kitchen_weekend_service_end_time') ? 'kitchen_weekend_service_end_time' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
