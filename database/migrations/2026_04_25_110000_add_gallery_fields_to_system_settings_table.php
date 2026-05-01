<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table): void {
            $table->json('home_hero_gallery_paths')->nullable()->after('hero_home_slide_three');
            $table->json('home_views_gallery_paths')->nullable()->after('home_hero_gallery_paths');
            $table->json('about_gallery_paths')->nullable()->after('home_views_gallery_paths');
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table): void {
            $table->dropColumn(['home_hero_gallery_paths', 'home_views_gallery_paths', 'about_gallery_paths']);
        });
    }
};
