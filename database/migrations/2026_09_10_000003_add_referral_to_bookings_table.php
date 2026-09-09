<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('partner_id')->nullable()->after('tour_id')->constrained('partners')->onDelete('set null');
            $table->string('referral_code')->nullable()->after('partner_id');
            $table->string('referral_source')->nullable()->after('referral_code');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['partner_id']);
            $table->dropColumn(['partner_id', 'referral_code', 'referral_source']);
        });
    }
};
