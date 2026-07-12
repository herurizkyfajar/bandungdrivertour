<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('Bandung Driver Tour');
            $table->text('company_address')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_website')->nullable();
            $table->string('signer_name')->default('Aldi Maulana');
            $table->string('signer_title')->default('CFO');
            $table->string('signature_path')->nullable();
            $table->string('bank_name')->default('Bank Central Asia (Bank Transfer)');
            $table->string('bank_account_number')->default('1394304240');
            $table->string('bank_account_name')->default('Aldi Maulana');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_settings');
    }
};
