<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offering_categories', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
        });

        Schema::table('offering_items', function (Blueprint $table) {
            $table->string('component')->nullable()->change();
            $table->string('remark')->nullable()->default(null)->change();
            $table->string('type', 10)->nullable()->default(null)->change();
            $table->decimal('quantity', 10, 2)->nullable()->default(null)->change();
            $table->decimal('unit_price', 15, 2)->nullable()->default(null)->change();
            $table->decimal('total_cost', 15, 2)->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('offering_items', function (Blueprint $table) {
            $table->string('component')->nullable(false)->change();
            $table->string('remark')->default('Included (Wajib)')->change();
            $table->enum('type', ['V', 'F'])->default('V')->change();
            $table->decimal('quantity', 10, 2)->default(1)->change();
            $table->decimal('unit_price', 15, 2)->default(0)->change();
            $table->decimal('total_cost', 15, 2)->default(0)->change();
        });

        Schema::table('offering_categories', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
        });
    }
};
