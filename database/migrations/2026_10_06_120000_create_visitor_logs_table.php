<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->nullable()->index();
            $table->string('session_id', 100)->nullable()->index();
            $table->string('path', 255)->index();
            $table->text('url')->nullable();
            $table->string('method', 10)->default('GET');
            $table->text('referer')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_type', 30)->default('desktop')->index(); // desktop, mobile, tablet, bot
            $table->string('browser', 50)->nullable()->index();
            $table->string('platform', 50)->nullable()->index();
            $table->string('country', 100)->default('Indonesia');
            $table->string('country_code', 10)->default('ID');
            $table->string('province', 100)->nullable()->index();
            $table->string('city', 100)->nullable()->index();
            $table->string('postal_code', 20)->nullable();
            $table->boolean('is_bot')->default(false);
            $table->timestamp('visited_at')->useCurrent()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_logs');
    }
};
