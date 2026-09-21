<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offerings', function (Blueprint $table) {
            $table->id();
            $table->string('offering_number')->unique();
            $table->string('client_name');
            $table->string('provider')->default('PT Travine Indonesia');
            $table->string('pic_client')->nullable();
            $table->string('pic_project')->nullable();
            $table->string('pic_contact')->nullable();
            $table->string('package_name');
            $table->string('date_duration')->nullable();
            $table->integer('participants')->default(1);
            $table->text('description')->nullable();
            $table->decimal('production_cost', 15, 2)->default(0);
            $table->decimal('other_cost_percentage', 5, 2)->default(5.00);
            $table->decimal('other_cost', 15, 2)->default(0);
            $table->decimal('management_fee_percentage', 5, 2)->default(15.00);
            $table->decimal('management_fee', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('price_per_pack', 15, 2)->default(0);
            $table->enum('status', ['draft', 'sent', 'revised', 'approved', 'rejected', 'expired'])->default('draft');
            $table->json('notes')->nullable();
            $table->json('revision_notes')->nullable();
            $table->text('recommendation')->nullable();
            $table->timestamps();
        });

        Schema::create('offering_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offering_id')->constrained('offerings')->onDelete('cascade');
            $table->string('name');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('offering_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offering_category_id')->constrained('offering_categories')->onDelete('cascade');
            $table->string('component');
            $table->text('description')->nullable();
            $table->string('remark')->default('Included (Wajib)');
            $table->enum('type', ['V', 'F'])->default('V');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('offering_master_items', function (Blueprint $table) {
            $table->id();
            $table->string('category_name');
            $table->string('component');
            $table->text('description')->nullable();
            $table->enum('default_type', ['V', 'F'])->default('V');
            $table->string('default_remark')->default('Included (Wajib)');
            $table->decimal('default_price', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offering_master_items');
        Schema::dropIfExists('offering_items');
        Schema::dropIfExists('offering_categories');
        Schema::dropIfExists('offerings');
    }
};
