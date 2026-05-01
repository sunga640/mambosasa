<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('system_settings', 'restaurant_integration_enabled')) {
                $table->boolean('restaurant_integration_enabled')->default(false)->after('mail_from_name');
            }
            if (! Schema::hasColumn('system_settings', 'restaurant_api_base_url')) {
                $table->string('restaurant_api_base_url')->nullable()->after('restaurant_integration_enabled');
            }
            if (! Schema::hasColumn('system_settings', 'restaurant_api_key')) {
                $table->text('restaurant_api_key')->nullable()->after('restaurant_api_base_url');
            }
            if (! Schema::hasColumn('system_settings', 'restaurant_api_secret')) {
                $table->text('restaurant_api_secret')->nullable()->after('restaurant_api_key');
            }
            if (! Schema::hasColumn('system_settings', 'restaurant_sso_shared_secret')) {
                $table->text('restaurant_sso_shared_secret')->nullable()->after('restaurant_api_secret');
            }
            if (! Schema::hasColumn('system_settings', 'restaurant_sso_entry_path')) {
                $table->string('restaurant_sso_entry_path')->nullable()->after('restaurant_sso_shared_secret');
            }
            if (! Schema::hasColumn('system_settings', 'restaurant_api_timeout_seconds')) {
                $table->unsignedSmallInteger('restaurant_api_timeout_seconds')->nullable()->after('restaurant_sso_entry_path');
            }
            if (! Schema::hasColumn('system_settings', 'restaurant_token_ttl_minutes')) {
                $table->unsignedSmallInteger('restaurant_token_ttl_minutes')->nullable()->after('restaurant_api_timeout_seconds');
            }
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $columns = [
                'restaurant_integration_enabled',
                'restaurant_api_base_url',
                'restaurant_api_key',
                'restaurant_api_secret',
                'restaurant_sso_shared_secret',
                'restaurant_sso_entry_path',
                'restaurant_api_timeout_seconds',
                'restaurant_token_ttl_minutes',
            ];

            $existing = array_values(array_filter($columns, fn (string $column): bool => Schema::hasColumn('system_settings', $column)));
            if ($existing !== []) {
                $table->dropColumn($existing);
            }
        });
    }
};
