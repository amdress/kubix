<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('branches', function (Blueprint $table) {
            $table->id();

            $table->string('name')->index();
            $table->string('slug')->unique();
            $table->string('code', 50)->nullable()->index();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();

            // El interruptor maestro: ¿Tiene local o es solo representación online?
            $table->boolean('is_physical')->default(false)->index();

            $table->json('branding')->nullable();

            $table->foreignId('territory_id')
                ->constrained('territories')
                ->restrictOnDelete();

            $table->enum('territory_level', ['country', 'state', 'city', 'neighborhood']);

            $table->string('territory_path')
                ->nullable()
                ->index();

            $table->boolean('is_active')->default(true)->index();

            $table->timestamp('activated_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['territory_id', 'is_active'], 'idx_branch_territory_active');
            $table->index(['territory_level'], 'idx_branch_territory_level');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('branches');
        Schema::enableForeignKeyConstraints();
    }
};
