<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('offering_id')->nullable()->constrained('offerings')->onDelete('set null');
            $table->string('client_name');
            $table->string('client_company')->nullable();
            $table->string('pic_client')->nullable();
            $table->string('pic_contact')->nullable();
            $table->string('project_name');
            $table->text('description')->nullable();
            $table->date('issue_date');
            $table->date('due_date');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('other_cost', 15, 2)->default(0);
            $table->decimal('management_fee', 15, 2)->default(0);
            $table->decimal('gross_total', 15, 2)->default(0);
            $table->decimal('pph_percentage', 5, 2)->default(2.00);
            $table->decimal('pph_amount', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->boolean('include_pph_deduction')->default(false);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('remaining_balance', 15, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'overdue', 'cancelled'])->default('unpaid');
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_holder')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('project_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_invoice_id')->constrained('project_invoices')->onDelete('cascade');
            $table->string('category_name');
            $table->string('component');
            $table->text('description')->nullable();
            $table->string('remark')->nullable();
            $table->string('type')->nullable();
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_invoice_items');
        Schema::dropIfExists('project_invoices');
    }
};
