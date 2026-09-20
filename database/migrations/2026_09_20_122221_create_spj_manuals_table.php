<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spj_manuals', function (Blueprint $table) {
            $table->id();
            $table->string('driver_name');
            $table->string('customer_name');
            $table->string('customer_contact')->nullable();
            $table->string('country_of_origin')->nullable();
            $table->unsignedSmallInteger('passenger_count')->default(1);
            $table->date('start_date');
            $table->time('pickup_time');
            $table->text('pickup_address');
            $table->string('service_type')->nullable();
            $table->string('service_duration')->nullable();
            $table->string('payment_plan')->nullable();
            $table->longText('trip_details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spj_manuals');
    }
};
