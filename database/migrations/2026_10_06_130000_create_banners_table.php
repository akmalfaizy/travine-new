<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('tag')->nullable(); // e.g. '🔥 Open Trip Terdekat', 'Diskon Spesial'
            $table->string('subtitle')->nullable();
            $table->string('image');
            $table->string('mobile_image')->nullable();
            $table->foreignId('tour_id')->nullable()->constrained('tours')->nullOnDelete();
            $table->string('link_url', 500)->nullable();
            $table->string('button_text', 50)->default('Lihat Paket');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_popup')->default(false);
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
