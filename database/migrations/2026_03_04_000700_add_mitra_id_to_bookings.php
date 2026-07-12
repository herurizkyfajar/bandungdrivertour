<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'mitra_id')) {
                $table->foreignId('mitra_id')->nullable()->constrained('mitras')->nullOnDelete()->after('vehicle_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'mitra_id')) {
                $table->dropConstrainedForeignId('mitra_id');
            }
        });
    }
};
