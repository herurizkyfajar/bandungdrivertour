<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'down_payment_amount')) {
                $table->decimal('down_payment_amount', 15, 2)->nullable()->after('payment_plan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'down_payment_amount')) {
                $table->dropColumn('down_payment_amount');
            }
        });
    }
};
