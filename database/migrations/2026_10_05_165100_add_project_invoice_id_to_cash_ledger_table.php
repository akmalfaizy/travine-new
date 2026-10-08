<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_ledger', function (Blueprint $table) {
            $table->foreignId('project_invoice_id')
                  ->nullable()
                  ->after('booking_id')
                  ->constrained('project_invoices')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('cash_ledger', function (Blueprint $table) {
            $table->dropForeign(['project_invoice_id']);
            $table->dropColumn('project_invoice_id');
        });
    }
};
