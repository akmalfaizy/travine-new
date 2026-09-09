<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->decimal('hpp_price', 12, 0)->default(0)->after('price');
            $table->decimal('commission_percentage', 5, 2)->default(0)->after('hpp_price');
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn(['hpp_price', 'commission_percentage']);
        });
    }
};
