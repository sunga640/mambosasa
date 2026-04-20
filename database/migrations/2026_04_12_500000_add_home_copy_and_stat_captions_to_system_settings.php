<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->string('home_hero_eyebrow', 160)->nullable();
            $table->string('home_hero_headline_suffix', 255)->nullable();
            $table->string('home_section1_heading', 255)->nullable();
            $table->text('home_section1_body')->nullable();
            $table->string('home_stat_caption_guests', 80)->nullable();
            $table->string('home_stat_caption_rooms', 80)->nullable();
            $table->string('home_stat_caption_pools', 80)->nullable();
            $table->string('home_stat_caption_dining', 80)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
                'home_hero_eyebrow',
                'home_hero_headline_suffix',
                'home_section1_heading',
                'home_section1_body',
                'home_stat_caption_guests',
                'home_stat_caption_rooms',
                'home_stat_caption_pools',
                'home_stat_caption_dining',
            ]);
        });
    }
};
