<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mitra_vehicle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mitra_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['mitra_id', 'vehicle_id']);
        });

        // Migrate existing mitra_id data to pivot table
        $vehicles = DB::table('vehicles')->whereNotNull('mitra_id')->get();
        foreach ($vehicles as $v) {
            DB::table('mitra_vehicle')->insert([
                'mitra_id' => $v->mitra_id,
                'vehicle_id' => $v->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mitra_vehicle');
    }
};
