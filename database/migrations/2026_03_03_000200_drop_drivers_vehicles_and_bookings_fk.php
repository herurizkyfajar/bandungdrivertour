<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                if (Schema::hasColumn('bookings', 'driver_id')) {
                    $table->dropConstrainedForeignId('driver_id');
                }
                if (Schema::hasColumn('bookings', 'vehicle_id')) {
                    $table->dropConstrainedForeignId('vehicle_id');
                }
            });
        }
        if (Schema::hasTable('vehicles')) {
            Schema::dropIfExists('vehicles');
        }
        if (Schema::hasTable('drivers')) {
            Schema::dropIfExists('drivers');
        }
    }

    public function down(): void
    {
        // Recreate minimal tables and columns for rollback
        if (!Schema::hasTable('drivers')) {
            Schema::create('drivers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone')->nullable();
                $table->string('license_number')->nullable();
                $table->string('status')->default('active');
                $table->boolean('is_partner')->default(true);
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('vehicles')) {
            Schema::create('vehicles', function (Blueprint $table) {
                $table->id();
                $table->string('plate_number')->unique();
                $table->string('make')->nullable();
                $table->string('model')->nullable();
                $table->unsignedInteger('capacity')->default(4);
                $table->string('status')->default('available');
                $table->decimal('price_per_day', 10, 2)->nullable();
                $table->timestamps();
            });
        }
        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                if (!Schema::hasColumn('bookings', 'vehicle_id')) {
                    $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
                }
                if (!Schema::hasColumn('bookings', 'driver_id')) {
                    $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
                }
            });
        }
    }
};
