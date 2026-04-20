<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_types', function (Blueprint $table): void {
            $table->unsignedInteger('max_rooms')->default(0)->after('price');
            $table->json('thumbnail_paths')->nullable()->after('hero_image_path');
        });
    }

    public function down(): void
    {
        Schema::table('room_types', function (Blueprint $table): void {
            $table->dropColumn(['max_rooms', 'thumbnail_paths']);
        });
    }
};
