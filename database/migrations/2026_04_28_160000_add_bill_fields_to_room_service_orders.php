<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_service_orders', function (Blueprint $table): void {
            if (! Schema::hasColumn('room_service_orders', 'bill_generated_at')) {
                $table->timestamp('bill_generated_at')->nullable()->after('paid_at');
            }

            if (! Schema::hasColumn('room_service_orders', 'bill_generated_by_user_id')) {
                $table->foreignId('bill_generated_by_user_id')->nullable()->after('bill_generated_at')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('room_service_orders', function (Blueprint $table): void {
            if (Schema::hasColumn('room_service_orders', 'bill_generated_by_user_id')) {
                $table->dropConstrainedForeignId('bill_generated_by_user_id');
            }

            if (Schema::hasColumn('room_service_orders', 'bill_generated_at')) {
                $table->dropColumn('bill_generated_at');
            }
        });
    }
};
