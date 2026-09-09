<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('partners')->onDelete('cascade');
            $table->foreignId('booking_id')->unique()->constrained('bookings')->onDelete('cascade');
            $table->foreignId('tour_id')->constrained('tours')->onDelete('cascade');
            $table->decimal('hpp_snapshot', 12, 0)->default(0);
            $table->decimal('publish_price_snapshot', 12, 0)->default(0);
            $table->decimal('margin_snapshot', 12, 0)->default(0);
            $table->decimal('commission_percentage_snapshot', 5, 2)->default(0);
            $table->decimal('commission_amount', 12, 0)->default(0);
            $table->enum('status', ['pending', 'approved', 'available', 'requested', 'processing', 'paid', 'rejected', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
