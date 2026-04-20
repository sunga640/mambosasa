<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->text('hero_home_image_url')->nullable()->after('hero_home_background');
            $table->text('inner_page_hero_image_url')->nullable()->after('hero_home_image_url');
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn(['hero_home_image_url', 'inner_page_hero_image_url']);
        });
    }
};
