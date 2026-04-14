<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla: Solutions (Catálogo Global de Productos/Módulos)
     * * Aquí se registran las "cajas de herramientas" que existen en el ecosistema.
     * * El SuperAdmin agrega soluciones aquí, y luego el Owner decide en qué Branch activarlas.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('solutions', function (Blueprint $table) {
            $table->id();

            // =====================================================
            // IDENTIDAD DE CATÁLOGO
            // =====================================================
            $table->string('name')->unique()
                  ->comment('Nombre comercial: Libera Juros, Gestión Inmobiliaria, etc.');
            
            $table->string('slug')->unique()->index()
                  ->comment('Identificador para rutas y lógica de código (libera-juros)');
            
            $table->text('description')->nullable()
                  ->comment('Explicación de qué resuelve esta solución');

            // =====================================================
            // CLASIFICACIÓN (Para el Marketplace interno)
            // =====================================================
            // core: Vitales, addon: Extras, vertical: Mercados específicos.
            $table->enum('type', ['core', 'addon', 'vertical'])->default('vertical')
                  ->comment('Categoría de la solución dentro de la plataforma');

            // Dominio funcional (abogacia, inmobiliaria, general)
            $table->string('domain')->nullable()
                  ->comment('Segmento de mercado al que pertenece');

            // =====================================================
            // INFRAESTRUCTURA (Vínculo con Git)
            // =====================================================
            // Como pediste: El backend ya debe estar listo. Esta es la rama base.
            $table->string('base_git_branch')->default('main')
                  ->comment('Rama de Git donde reside el código base de esta solución');

            // =====================================================
            // METADATOS Y CONFIGURACIÓN
            // =====================================================
            // features: ['pago_online', 'reportes_pdf'] - Para mostrar en el Wizard.
            $table->json('features')->nullable()
                  ->comment('Lista de funcionalidades incluidas para mostrar al cliente');

            // config_schema: JSON Schema que define qué datos necesita esta solución
            // para activarse (ej: Token de API de bancos, Credenciales, etc).
            $table->json('config_schema')->nullable()
                  ->comment('Estructura de datos necesaria para la configuración inicial');

            // =====================================================
            // VISIBILIDAD Y CONTROL
            // =====================================================
            $table->boolean('is_active')->default(true)
                  ->comment('Define si la solución puede ser contratada actualmente');
                  
            $table->boolean('is_public')->default(true)
                  ->comment('Si es false, solo el SuperAdmin puede verla/asignarla');

            // =====================================================
            // AUDITORÍA
            // =====================================================
            $table->timestamps();
            $table->softDeletes();

            // Índices para búsquedas rápidas en el catálogo
            $table->index(['type', 'domain'], 'idx_solution_catalog');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::dropIfExists('solutions');
    }
};