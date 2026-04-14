<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla: addresses
     *
     * Representa puntos físicos reales dentro del mundo.
     * Se utiliza para:
     *   - Ubicación física de sucursales
     *   - Dirección de usuarios
     *   - Oficinas, bodegas, locales, etc.
     *
     * NO representa áreas, regiones ni zonas administrativas.
     * NO almacena información territorial ni GeoJSON.
     *
     * Cada registro es un punto físico exacto en el mapa.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            // Relación polimórfica con el objeto dueño de la dirección
            $table->morphs('addressable');

            // Etiqueta de la dirección (Casa, Oficina, Matriz, etc)
            $table->string('label')->nullable();

            $table->string('zip_code', 20)->nullable();
            $table->string('street', 150)->nullable();
            $table->string('number', 20)->nullable();
            $table->string('complement', 150)->nullable();
            $table->string('reference')->nullable();

            // Coordenadas exactas del punto físico
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Marca la dirección principal
            $table->boolean('is_primary')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['addressable_type', 'addressable_id', 'is_primary'], 'idx_addressable_primary');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};