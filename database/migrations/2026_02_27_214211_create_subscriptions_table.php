<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Subscriptions — Contrato de pago entre una Company y un Plan.
 *
 * RESPONSABILIDAD:
 *   Registra QUÉ plan contrató una Company, en QUÉ territorio,
 *   cuánto pagó y por cuánto tiempo está activo.
 *
 * RELACIÓN CON ACCOUNT:
 *   Account → dice QUÉ solución está activa (Aluggap, LibreJuros, publicidad)
 *   Subscription → dice CÓMO se paga esa solución (plan, precio, vigencia)
 *   Una Account puede tener múltiples Subscriptions (historial de renovaciones)
 *
 * ESTADOS:
 *   pending   → creada, esperando primer pago
 *   active    → pago confirmado, servicio activo
 *   expired   → venció sin renovar
 *   cancelled → cancelada manualmente
 *
 * PARA EL DASHBOARD:
 *   revenue → SUM payments.amount WHERE status = paid
 *   mrr     → SUM subscriptions.price_paid WHERE status = active AND billing_cycle = monthly
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            // Contrato de solución que origina esta suscripción
            $table->foreignId('account_id')
                ->constrained('accounts')
                ->cascadeOnDelete();

            // Plan contratado
            $table->foreignId('plan_id')
                ->constrained('plans')
                ->restrictOnDelete();

            // Branch donde aplica (territorio de la publicidad o solución)
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->restrictOnDelete();

            // Precio que pagó en el momento de contratar
            // Guardamos el precio histórico — si el plan cambia de precio, esto no cambia
            $table->decimal('price_paid', 10, 2);
            $table->char('currency', 3)->default('BRL');

            $table->enum('status', [
                'pending',
                'active',
                'expired',
                'cancelled',
            ])->default('pending');

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices para las queries del dashboard
            $table->index(['branch_id', 'status']);
            $table->index(['account_id', 'status']);
            $table->index(['status', 'ends_at']);
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};