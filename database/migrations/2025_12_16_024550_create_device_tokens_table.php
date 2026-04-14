<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();

            // ============================
            // RELATION
            // ============================
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // ============================
            // TOKEN INFO
            // ============================
            $table->string('token')->unique();
            $table->string('provider', 20);   // expo | fcm | apns
            $table->string('platform', 20);   // ios | android | web

            // ============================
            // DEVICE META
            // ============================
            $table->string('device_name')->nullable();
            $table->string('app_version', 20)->nullable();

            // ============================
            // STATUS
            // ============================
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();

            // ============================
            // AUDITORÍA
            // ============================
            $table->timestamps();

            // ============================
            // INDEXES
            // ============================
            $table->index(['user_id', 'is_active']);
            $table->index('provider');
            $table->index('platform');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::dropIfExists('device_tokens');
    }
};
