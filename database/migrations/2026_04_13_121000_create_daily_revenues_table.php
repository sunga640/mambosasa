<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_revenues', function (Blueprint $table): void {
            $table->id();
            $table->date('revenue_date')->unique();
            $table->decimal('amount_total', 14, 2)->default(0);
            $table->unsignedInteger('bookings_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_revenues');
    }
};
