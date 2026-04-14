<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Payments — Historial de todos los cobros de la plataforma.
 *
 * RESPONSABILIDAD:
 *   Registro inmutable de cada intento de cobro.
 *   Nunca se edita — solo se agregan registros nuevos.
 *   Si un pago falla y se reintenta, es un nuevo Payment.
 *
 * RELACIÓN CON SUBSCRIPTION:
 *   Subscription → el contrato vigente
 *   Payment      → cada cobro individual de esa suscripción
 *   Una Subscription tiene múltiples Payments (renovaciones mensuales, reintentos)
 *
 * MÉTODOS DE PAGO:
 *   pix          → QR Code instantáneo (Brasil)
 *   stripe       → tarjeta internacional
 *   mercadopago  → tarjeta + wallet regional (LATAM)
 *   manual       → registrado manualmente por superadmin
 *
 * ESTADOS:
 *   pending  → iniciado, esperando confirmación del gateway
 *   paid     → confirmado y acreditado
 *   failed   → rechazado o expirado
 *   refunded → devuelto (cancelación con reembolso)
 *
 * PARA EL DASHBOARD:
 *   revenue semanal  → SUM(amount) WHERE status=paid AND paid_at >= hace 7 días
 *   revenue mensual  → SUM(amount) WHERE status=paid AND paid_at >= hace 30 días
 *   trend            → comparar período actual vs período anterior
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Suscripción que origina el cobro
            $table->foreignId('subscription_id')
                ->constrained('subscriptions')
                ->restrictOnDelete();

            // Company que paga — redundante pero útil para queries directas sin joins
            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete();

            // Branch del territorio — para calcular revenue por zona sin joins complejos
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->restrictOnDelete();

            $table->decimal('amount', 10, 2);
            $table->char('currency', 3)->default('BRL');

            $table->enum('method', [
                'pix',
                'stripe',
                'mercadopago',
                'manual',
            ])->default('pix');

            $table->enum('status', [
                'pending',
                'paid',
                'failed',
                'refunded',
            ])->default('pending');

            // ID del gateway externo para reconciliación
            // Ej: charge_id de Stripe, payment_id de MercadoPago
            $table->string('gateway_id')->nullable();
            $table->json('gateway_response')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            // Índices críticos para el dashboard
            // Todas las queries de revenue usan branch_id + status + paid_at
            $table->index(['branch_id', 'status', 'paid_at']);
            $table->index(['company_id', 'status']);
            $table->index(['subscription_id', 'status']);
            $table->index('gateway_id');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};