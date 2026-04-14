<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla: companies
     *
     * Representa la IDENTIDAD DE NEGOCIO dentro de Kubix.
     *
     * Una Company es cualquier negocio que opera en la plataforma,
     * desde un vendedor informal de helados hasta un estudio jurídico.
     *
     * REGLAS CLAVE:
     *   - La legalidad (CNPJ) NO es requisito de entrada
     *   - La legalidad ES requisito de ciertas soluciones (ej: LibreJuros)
     *   - El contrato de soluciones vive en Account, no aquí
     *   - Los miembros (owner, staff) se vinculan via Affiliation
     *
     * FLUJO DE VIDA:
     *   1. Usuario acepta términos de la plataforma al registrarse
     *   2. Registra su Company (mínimo: branch, tipo, nombre)
     *   3. Completa perfil (branding, docs, CNPJ si aplica)
     *   4. Accede al marketplace → contrata soluciones → Account creado
     *   5. Soluciones formales exigen CNPJ y KYC antes de activarse
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            // =====================================================
            // TERRITORIO (DÓNDE VIVE LA EMPRESA)
            // =====================================================

            /**
             * Branch a la que pertenece la empresa.
             *
             * Define el territorio operativo del negocio.
             * Permite filtrar empresas por zona para KPIs y publicidad.
             * Ej: "dame todas las empresas activas de Pinheirinho"
             */
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnDelete()
                ->comment('Branch (territorio) donde opera la empresa');

            // =====================================================
            // TIPO DE EMPRESA
            // =====================================================

            /**
             * Define la naturaleza legal del negocio.
             *
             * informal → sin CNPJ, acceso limitado a soluciones
             * formal   → con CNPJ, acceso completo a todas las soluciones
             *
             * La validación ocurre al contratar soluciones que lo requieren,
             * no al crear la empresa.
             */
            $table->enum('type', ['informal', 'formal'])
                ->default('informal')
                ->index()
                ->comment('Tipo: informal (sin CNPJ) | formal (con CNPJ)');

            // =====================================================
            // IDENTIDAD DE NEGOCIO
            // =====================================================

            $table->string('name', 150)
                ->comment('Nombre legal o de registro');

            $table->string('trade_name', 150)
                ->nullable()
                ->comment('Nombre comercial visible en la plataforma');

            $table->string('slug')
                ->unique()
                ->index()
                ->comment('Slug único para URLs y referencias');

            // =====================================================
            // IDENTIDAD LEGAL (OPCIONAL / REQUERIDA POR SOLUCIONES)
            // =====================================================

            /**
             * CNPJ del negocio.
             * Null para empresas informales.
             * Requerido por soluciones que exigen legalidad.
             */
            $table->string('cnpj', 20)
                ->nullable()
                ->unique()
                ->index()
                ->comment('CNPJ — requerido solo para soluciones formales');

            // =====================================================
            // KYC — VERIFICACIÓN BÁSICA DEL NEGOCIO
            // =====================================================

            /**
             * Indica si el representante del negocio fue verificado.
             *
             * La verificación mínima es la foto del documento de identidad
             * (CPF/cédula) subida via Spatie Media en la colección
             * 'identity_document' del User representante.
             *
             * false → pendiente de verificación
             * true  → documento recibido y revisado por Kubix
             *
             * Este flag lo activa el superadmin o un proceso automático,
             * nunca el propio usuario.
             */
            $table->boolean('is_verified')
                ->default(false)
                ->index()
                ->comment('Si el representante fue verificado (KYC básico)');

            $table->timestamp('verified_at')
                ->nullable()
                ->comment('Fecha en que se completó la verificación KYC');

            // =====================================================
            // CONTACTO BÁSICO
            // =====================================================

            $table->string('email', 150)
                ->nullable()
                ->comment('Email de contacto del negocio');

            $table->string('phone', 20)
                ->nullable()
                ->comment('Teléfono de contacto');

            // =====================================================
            // BRANDING
            // =====================================================

            /**
             * Identidad visual del negocio.
             *
             * Estructura esperada:
             * {
             *   "primary_color": "#ff6b6b",
             *   "logo": null,
             *   "watermark": null
             * }
             *
             * Archivos físicos se gestionan via Spatie Media Library
             * en colecciones: company_logo, company_photos, company_documents
             */
            $table->json('branding')
                ->nullable()
                ->comment('Identidad visual: primary_color, logo, watermark');

            // =====================================================
            // REDES SOCIALES
            // =====================================================

            /**
             * Links de redes sociales del negocio.
             *
             * Estructura esperada:
             * {
             *   "instagram": "https://instagram.com/...",
             *   "facebook": null,
             *   "whatsapp": "5592999999999",
             *   "x": null
             * }
             */
            $table->json('social_links')
                ->nullable()
                ->comment('Redes sociales del negocio');

            // =====================================================
            // ESTADO
            // =====================================================

            $table->boolean('is_active')
                ->default(true)
                ->index()
                ->comment('Si la empresa está activa en la plataforma');

            // =====================================================
            // LARAVEL
            // =====================================================

            $table->timestamps();
            $table->softDeletes();

            // =====================================================
            // ÍNDICES COMPUESTOS
            // =====================================================

            // Dashboard: empresas activas por territorio
            $table->index(['branch_id', 'is_active']);

            // Filtrar por tipo dentro de un territorio
            $table->index(['branch_id', 'type']);

            // KYC pendiente: verificaciones por hacer
            $table->index(['is_verified', 'is_active']);
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};