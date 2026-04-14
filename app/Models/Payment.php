<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Payment
 *
 * Registro inmutable de cada cobro en la plataforma.
 *
 * REGLAS:
 *   - Nunca se edita un Payment existente
 *   - Si un pago falla y se reintenta → nuevo Payment
 *   - Si se reembolsa → nuevo Payment con status = refunded
 *   - Sin SoftDeletes — los pagos son registros contables permanentes
 *
 * branch_id y company_id son redundantes pero críticos
 * para queries del dashboard sin joins complejos.
 *
 * MÉTODOS:
 *   pix          → QR Code instantáneo (Brasil)
 *   stripe       → tarjeta internacional
 *   mercadopago  → tarjeta + wallet regional (LATAM)
 *   manual       → registrado manualmente por superadmin
 *
 * ESTADOS:
 *   pending  → iniciado, esperando confirmación del gateway
 *   paid     → confirmado y acreditado
 *   failed   → rechazado o expirado
 *   refunded → devuelto
 */
class Payment extends Model
{
    use LogsActivity;

    protected $table = 'payments';

    protected $fillable = [
        'subscription_id',
        'company_id',
        'branch_id',
        'amount',
        'currency',
        'method',
        'status',
        'gateway_id',
        'gateway_response',
        'paid_at',
    ];

    protected $casts = [
        'amount'           => 'float',
        'gateway_response' => 'array',
        'paid_at'          => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(
                fn (string $event) => "Payment #{$this->id} {$event} — {$this->amount} {$this->currency}"
            );
    }

    // =========================================================================
    // RELACIONES
    // =========================================================================

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Solo pagos confirmados — el único estado que cuenta para revenue.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeByCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Pagos dentro de un rango de fechas.
     * Usado por el dashboard para calcular revenue por período.
     */
    public function scopeInPeriod($query, $from, $to)
    {
        return $query->whereBetween('paid_at', [$from, $to]);
    }

    /**
     * Pagos de las últimas N semanas.
     */
    public function scopeLastWeeks($query, int $weeks = 1)
    {
        return $query->where('paid_at', '>=', now()->subWeeks($weeks));
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    public function isPaid(): bool     { return $this->status === 'paid'; }
    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isFailed(): bool   { return $this->status === 'failed'; }
    public function isRefunded(): bool { return $this->status === 'refunded'; }
}