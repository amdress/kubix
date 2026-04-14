<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('accounts', function (Blueprint $table) {
            $table->id();

            // =====================================================
            // CONTEXTO OPERATIVO
            // =====================================================

            // ¿Dónde opera? (Ciudad / Región / Digital)
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnDelete();

            // ¿Qué software usa?
            $table->foreignId('solution_id')
                ->constrained('solutions')
                ->cascadeOnDelete();

            // Slug duplicado SOLO para acceso rápido en código
            // (evita joins en middlewares / guards)
            $table->string('solution_slug', 100)->index();

            // =====================================================
            // DUEÑO DEL CONTRATO
            // =====================================================
            // Normalmente Company, pero puede ser User en casos simples
            $table->morphs('accountable');

            // =====================================================
            // BRANDING NIVEL CUENTA
            // =====================================================
            // Overrides de branding respecto a Branch / Company
            $table->json('branding')->nullable();

            // =====================================================
            // CICLO DE VIDA DEL CONTRATO
            // =====================================================
            // pending  -> creado pero no completo
            // active   -> operativo
            // suspended-> bloqueado
            // cancelled-> terminado
            $table->string('status', 20)->default('pending')->index();

            $table->boolean('is_active')->default(true)->index();

            // Fechas clave (producto maduro)
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // =====================================================
            // AUDITORÍA
            // =====================================================
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // =====================================================
            // REGLA DE ORO
            // =====================================================
            // Una empresa no puede tener la MISMA solución
            // dos veces en la MISMA branch
            $table->unique(
                ['branch_id', 'solution_id', 'accountable_id', 'accountable_type'],
                'unique_account_context'
            );
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('accounts');
        Schema::enableForeignKeyConstraints();
    }
};
