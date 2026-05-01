<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->string('hero_home_slide_two')->nullable()->after('hero_home_background');
            $table->string('hero_home_slide_three')->nullable()->after('hero_home_slide_two');
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn(['hero_home_slide_two', 'hero_home_slide_three']);
        });
    }
};
