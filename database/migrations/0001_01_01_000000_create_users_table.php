<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // =====================================================
            // PERSONAL DATA (IDENTIDAD LEGAL)
            // =====================================================
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();

            $table->string('cpf', 14)->nullable()->unique();
            $table->string('phone', 20)->nullable();

            // =====================================================
            // AUTH
            // =====================================================
            $table->string('password');

            // =====================================================
            // TÉRMINOS Y CONDICIONES
            // =====================================================

            /**
             * Timestamp de aceptación de los términos de la plataforma.
             *
             * Se registra en el momento del registro.
             * Sin aceptación, el usuario no puede operar en Kubix.
             * Es evidencia legal — nunca se borra.
             *
             * La versión permite saber si necesita re-aceptar
             * cuando los términos sean actualizados.
             */
            $table->timestamp('terms_accepted_at')
                ->nullable()
                ->comment('Fecha en que aceptó los términos de la plataforma');

            $table->string('terms_version', 10)
                ->nullable()
                ->comment('Versión de los términos aceptados — ej: v1.0, v2.1');

            // =====================================================
            // KYC — VERIFICACIÓN DE IDENTIDAD
            // =====================================================

            /**
             * Indica si el usuario fue verificado con documento de identidad.
             *
             * El archivo físico (foto CPF/cédula) se almacena via
             * Spatie Media Library en colección 'identity_document'.
             *
             * false → pendiente
             * true  → verificado por Kubix
             *
             * Solo el superadmin o proceso automático activa este flag.
             */
            $table->boolean('identity_verified')
                ->default(false)
                ->index()
                ->comment('Si el usuario fue verificado con documento de identidad');

            $table->timestamp('identity_verified_at')
                ->nullable()
                ->comment('Fecha de verificación de identidad');

            // =====================================================
            // SECURITY & AUDIT
            // =====================================================
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->timestamp('password_changed_at')->nullable();
            $table->timestamp('blocked_at')->nullable();

            // =====================================================
            // HIERARCHY / TRACEABILITY
            // =====================================================
            $table->foreignId('registered_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Usuario que lo registró — manager que creó al staff');

            // =====================================================
            // LARAVEL CORE
            // =====================================================
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // =====================================================
            // INDEXES
            // =====================================================
            $table->index('last_login_at');
            $table->index('cpf');
            $table->index(['registered_by', 'created_at']);
            $table->index(['identity_verified', 'created_at']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::enableForeignKeyConstraints();
    }
};