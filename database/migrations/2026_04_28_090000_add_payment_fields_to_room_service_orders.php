<?php

use App\Models\RoomServiceOrder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_service_orders', function (Blueprint $table) {
            $table->string('public_reference')->nullable()->after('id');
            $table->foreignId('booking_method_id')->nullable()->after('hotel_branch_id')->constrained('booking_methods')->nullOnDelete();
            $table->string('payment_status', 32)->default('unpaid')->after('status');
            $table->string('payment_reference')->nullable()->after('payment_status');
            $table->timestamp('paid_at')->nullable()->after('payment_reference');
        });

        RoomServiceOrder::query()->orderBy('id')->get()->each(function (RoomServiceOrder $order): void {
            $order->forceFill([
                'public_reference' => $order->public_reference ?: RoomServiceOrder::nextReference(),
                'payment_status' => $order->payment_status ?: 'unpaid',
            ])->saveQuietly();
        });

        Schema::table('room_service_orders', function (Blueprint $table) {
            $table->unique('public_reference');
            $table->index(['payment_status', 'paid_at']);
        });
    }

    public function down(): void
    {
        Schema::table('room_service_orders', function (Blueprint $table) {
            $table->dropIndex(['payment_status', 'paid_at']);
            $table->dropUnique(['public_reference']);
            $table->dropConstrainedForeignId('booking_method_id');
            $table->dropColumn(['public_reference', 'payment_status', 'payment_reference', 'paid_at']);
        });
    }
};
