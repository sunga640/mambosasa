<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table): void {
            if (! Schema::hasColumn('system_settings', 'kitchen_alert_email_list')) {
                $table->json('kitchen_alert_email_list')->nullable()->after('kitchen_weekend_service_end_time');
            }
            if (! Schema::hasColumn('system_settings', 'kitchen_alert_phone_list')) {
                $table->json('kitchen_alert_phone_list')->nullable()->after('kitchen_alert_email_list');
            }
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table): void {
            $columns = array_values(array_filter([
                Schema::hasColumn('system_settings', 'kitchen_alert_email_list') ? 'kitchen_alert_email_list' : null,
                Schema::hasColumn('system_settings', 'kitchen_alert_phone_list') ? 'kitchen_alert_phone_list' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
