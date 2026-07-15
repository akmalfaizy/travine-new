<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // dp = bayar DP Rp100rb/pax, lunas = bayar penuh
            $table->enum('payment_type', ['dp', 'lunas'])->default('dp')->after('payment_status');
            $table->unsignedBigInteger('paid_amount')->default(0)->after('payment_type');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'paid_amount']);
        });
    }
};
