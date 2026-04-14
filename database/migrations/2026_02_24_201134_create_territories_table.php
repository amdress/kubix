<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('territories', function (Blueprint $table) {
            $table->id();

            // Clasificación KUBIX
            $table->enum('type', ['country', 'state', 'city', 'neighborhood'])->index();
            $table->string('code', 20)->nullable()->index(); // ISO, Prefijos, etc.
            $table->string('name');
            $table->string('normalized_name')->index(); // Para búsquedas insensibles
            $table->string('slug')->unique();

            // Jerarquía (Adjacency List + Path Enumeration)
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('territories')
                ->restrictOnDelete();

            $table->string('path')->nullable()->index(); // Ej: /1/5/12/
            $table->unsignedInteger('depth')->default(0);

            // Datos Geográficos (GeoJSON)
            // Representación de la ZONA (Polígono)
            $table->geometry('boundary')->nullable();

            // Representación del PUNTO (Centro de la zona)
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // El motor de búsqueda espacial
            // $table->spatialIndex('boundary');

            $table->timestamps();
            $table->softDeletes();
            $table->boolean('is_active')->default(true)->index();

            // Índices de Integridad y Búsqueda Rápida
            $table->unique(['parent_id', 'type', 'name'], 'uq_territory_parent_type_name');
            $table->index(['type', 'parent_id'], 'idx_territory_type_parent');
            $table->index(['path', 'type'], 'idx_territory_path_type');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::dropIfExists('territories');
    }
};
