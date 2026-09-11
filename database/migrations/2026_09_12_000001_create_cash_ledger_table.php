<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_ledger', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['in', 'out'])->comment('in = masuk, out = keluar');
            $table->decimal('amount', 15, 0)->default(0);
            $table->text('note')->nullable()->comment('Keterangan transaksi');
            $table->string('source', 50)->default('manual')
                  ->comment('manual = input admin, booking = otomatis dari booking, withdrawal = penarikan komisi');
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('balance_after', 15, 0)->default(0)->comment('Saldo kas setelah transaksi ini');
            $table->string('created_by')->nullable()->comment('Nama admin yang membuat transaksi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_ledger');
    }
};
