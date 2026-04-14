<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla: Branch Solutions (La Activación Real)
     * * Esta tabla conecta una sucursal con una solución del catálogo.
     * * Es aquí donde ocurre la magia: Pedro elige "Libera Juros" para "Curitiba".
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('branch_solutions', function (Blueprint $table) {
            $table->id();

            // =====================================================
            // RELACIONES (Quién tiene qué)
            // =====================================================
            $table->foreignId('branch_id')
                ->comment('La sucursal (Ciudad, Barrio o Local) que usará la solución')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('solution_id')
                ->comment('La solución técnica del catálogo (ej: Libera Juros)')
                ->constrained()
                ->cascadeOnDelete();

            // =====================================================
            // PERSONALIZACIÓN LOCAL
            // =====================================================
            // settings: Aquí guardamos la configuración específica para ESTA sucursal.
            // Ej: Token de API propio de la sucursal, límites de crédito locales, etc.
            $table->json('settings')
                ->nullable()
                ->comment('Sobreescritura de configuración técnica para esta sucursal específica');

            // pricing: Por si la solución cuesta diferente en un barrio rico que en la periferia.
            $table->json('pricing')
                ->nullable()
                ->comment('Esquema de precios personalizado para esta sucursal');

            // =====================================================
            // CICLO DE VIDA
            // =====================================================
            $table->boolean('is_active')
                ->default(true)
                ->comment('Switch para apagar/encender la solución en esta sucursal sin borrarla');
            
            $table->timestamp('enabled_at')
                ->nullable()
                ->comment('Fecha en la que el Owner activó esta solución');
                
            $table->timestamp('disabled_at')
                ->nullable()
                ->comment('Fecha en la que se dio de baja la solución');

            // =====================================================
            // AUDITORÍA
            // =====================================================
            $table->timestamps();
            $table->softDeletes();

            // =====================================================
            // INTEGRIDAD (Regla de Oro)
            // =====================================================
            // Evita que una sucursal tenga la misma solución duplicada.
            $table->unique(
                ['branch_id', 'solution_id'],
                'branch_solution_unique'
            );
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_solutions');
    }
};