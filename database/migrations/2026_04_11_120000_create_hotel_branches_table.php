<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('location_address');
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->boolean('is_ground_floor_only')->default(false);
            $table->unsignedTinyInteger('floors_count')->default(1);
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_whatsapp')->nullable();
            $table->text('extra_notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('logo_path')->nullable();
            $table->json('preview_images')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_branches');
    }
};
