<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('push_subscriptions')) {
            Schema::create('push_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('endpoint', 700);
                $table->string('public_key');
                $table->string('auth_token');
                $table->string('content_encoding')->default('aesgcm');
                $table->timestamps();
            });
        }

        DB::statement('ALTER TABLE push_subscriptions MODIFY endpoint VARCHAR(700) NOT NULL');

        Schema::table('push_subscriptions', function (Blueprint $table) {
            $table->unique(['user_id', 'endpoint'], 'push_subscriptions_user_id_endpoint_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
    }
};
