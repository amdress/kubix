<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Plans — Catálogo de planes disponibles en Kubix.
 *
 * Un plan define QUÉ se ofrece y CUÁNTO cuesta por defecto.
 * El precio puede variar por territorio en el futuro (plan_prices).
 *
 * TIPOS:
 *   advertising → publicidad territorial (default para todas las empresas)
 *   onboarding  → personalización para empresas formales (landing page, branding)
 *   solution    → soluciones específicas (Aluggap, LibreJuros, etc.)
 *   franchise   → membresía de franquiciado de una Branch
 *
 * CICLOS:
 *   weekly  → semanal  (publicidad básica)
 *   monthly → mensual  (soluciones, onboarding)
 *   yearly  → anual    (descuento por volumen)
 *   once    → pago único (onboarding fee)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints(); 


        Schema::create('plans', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->enum('type', [
                'advertising',
                'onboarding',
                'solution',
                'franchise',
            ])->default('advertising');

            $table->enum('billing_cycle', [
                'weekly',
                'monthly',
                'yearly',
                'once',
            ])->default('monthly');

            // Precio base global
            // En el futuro se sobreescribe por territorio via plan_prices
            $table->decimal('default_price', 10, 2)->default(0.00);
            $table->char('currency', 3)->default('BRL');

            // Vincula el plan a una Solution específica si aplica
            // Ej: "Aluggap Mensual" solo para companies con Account aluggap
            $table->foreignId('solution_id')
                ->nullable()
                ->constrained('solutions')
                ->nullOnDelete();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'is_active']);
            $table->index(['billing_cycle', 'is_active']);
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};