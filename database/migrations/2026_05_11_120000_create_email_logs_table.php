<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50)->default('invoice_notification');
            $table->string('to_email');
            $table->string('subject')->nullable();
            $table->string('status', 20);
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['invoice_id']);
            $table->index(['booking_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
