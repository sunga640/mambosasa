<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_methods', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_methods', 'code')) {
                $table->string('code', 120)->nullable()->after('name');
            }
            if (!Schema::hasColumn('booking_methods', 'method_type')) {
                $table->string('method_type', 20)->default('offline')->after('slug');
            }
            if (!Schema::hasColumn('booking_methods', 'show_on_booking_page')) {
                $table->boolean('show_on_booking_page')->default(true)->after('visibility');
            }
            if (!Schema::hasColumn('booking_methods', 'account_number')) {
                $table->string('account_number', 120)->nullable()->after('show_on_booking_page');
            }
            if (!Schema::hasColumn('booking_methods', 'account_holder')) {
                $table->string('account_holder', 150)->nullable()->after('account_number');
            }
            if (!Schema::hasColumn('booking_methods', 'instructions')) {
                $table->text('instructions')->nullable()->after('account_holder');
            }
            if (!Schema::hasColumn('booking_methods', 'gateway_public_key')) {
                $table->string('gateway_public_key', 255)->nullable()->after('instructions');
            }
            if (!Schema::hasColumn('booking_methods', 'gateway_secret_key')) {
                $table->string('gateway_secret_key', 255)->nullable()->after('gateway_public_key');
            }
            if (!Schema::hasColumn('booking_methods', 'gateway_base_url')) {
                $table->string('gateway_base_url', 255)->nullable()->after('gateway_secret_key');
            }
        });

        DB::table('booking_methods')
            ->where('slug', 'pay_later')
            ->update([
                'name' => 'Pay on arrival', // Nimesahihisha herufi 'arrival'
                'method_type' => 'offline',
                'show_on_booking_page' => true,
            ]);
    }

    public function down(): void
    {
        Schema::table('booking_methods', function (Blueprint $table) {
            $cols = [
                'code', 'method_type', 'show_on_booking_page', 'account_number',
                'account_holder', 'instructions', 'gateway_public_key',
                'gateway_secret_key', 'gateway_base_url'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('booking_methods', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
