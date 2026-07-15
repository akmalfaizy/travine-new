<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add username and role to users
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('name');
            $table->string('role')->default('admin')->after('username');
        });

        // Categories (Kategori Wisata)
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tours (Paket Wisata)
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('thumbnail')->nullable();
            $table->json('gallery')->nullable();
            $table->decimal('price', 12, 0)->default(0);
            $table->string('location')->nullable();
            $table->string('duration')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->text('itinerary')->nullable();
            $table->text('facilities')->nullable();
            $table->text('terms')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // News (Berita)
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('thumbnail')->nullable();
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->string('author')->default('Admin');
            $table->timestamp('published_at')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();
        });

        // Bookings (Pemesanan Wisata)
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->onDelete('cascade');
            $table->string('booking_code')->unique();
            $table->string('invoice_number')->unique()->nullable();
            $table->string('name');
            $table->string('whatsapp');
            $table->string('email');
            $table->date('departure_date');
            $table->integer('participants');
            $table->decimal('total_price', 12, 0)->default(0);
            $table->text('notes')->nullable();
            $table->string('payment_proof')->nullable();
            $table->enum('payment_status', ['unpaid', 'dp_paid', 'paid', 'refund', 'failed'])->default('unpaid');
            $table->enum('booking_status', ['waiting_payment', 'waiting_verification', 'processing', 'confirmed', 'departed', 'completed', 'cancelled'])->default('waiting_payment');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        // Notification Logs
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->string('recipient');
            $table->enum('type', ['Email', 'WhatsApp']);
            $table->enum('status', ['success', 'failed'])->default('success');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        // Activity Logs
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('description');
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });

        // Settings
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role']);
        });
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('news');
        Schema::dropIfExists('tours');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('settings');
    }
};
