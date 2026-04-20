<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_branch_id')->constrained('hotel_branches')->cascadeOnDelete();
            $table->unsignedTinyInteger('floor_number')->default(0);
            $table->string('name');
            $table->string('slug');
            $table->string('status', 32);
            $table->decimal('price', 12, 2);
            $table->text('description')->nullable();
            $table->string('video_path')->nullable();
            $table->timestamps();

            $table->unique(['hotel_branch_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
