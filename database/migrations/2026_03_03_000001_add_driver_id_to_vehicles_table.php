<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'driver_id')) {
                $table->foreignId('driver_id')->nullable()->constrained('drivers')->cascadeOnUpdate()->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (Schema::hasColumn('vehicles', 'driver_id')) {
                $table->dropConstrainedForeignId('driver_id');
            }
        });
    }
};
