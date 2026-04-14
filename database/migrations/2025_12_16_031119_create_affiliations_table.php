<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('affiliations', function (Blueprint $table) {
            $table->id();

            // El Usuario
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // CONTEXTO POLIMÓRFICO (Branch, Account, etc.)
            $table->morphs('affiliatable');

            // =====================================================
            // EL SCOPE (LA LLAVE MAESTRA)
            // =====================================================
            // Aquí guardamos el path del territorio vinculado al affiliatable.
            // Ej: Si es una Branch en Batel, aquí va /1/1/1/1/
            $table->string('path', 255)->index();
            $table->integer('depth')->default(0);

            // ROL CONTEXTUAL (manager, owner, staff, etc.)
            $table->string('role', 50)->index();

            $table->boolean('is_active')->default(true)->index();

            // AUDITORÍA
            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // REGLA DE INTEGRIDAD
            $table->unique(
                ['user_id', 'affiliatable_id', 'affiliatable_type', 'role'],
                'unique_user_affiliation_role'
            );
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('affiliations');
        Schema::enableForeignKeyConstraints();
    }
};
