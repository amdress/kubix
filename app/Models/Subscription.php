<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Subscription
 *
 * Contrato de pago entre una Company (via Account) y un Plan.
 *
 * RELACIÓN CON ACCOUNT:
 *   Account      → QUÉ solución está activa (Aluggap, publicidad, etc.)
 *   Subscription → CÓMO se paga esa solución (plan, precio, vigencia)
 *
 * Una Account puede tener múltiples Subscriptions a lo largo del tiempo
 * (historial de renovaciones). Solo una debería estar activa a la vez
 * por plan.
 *
 * ESTADOS:
 *   pending   → creada, esperando primer pago
 *   active    → pago confirmado, servicio activo
 *   expired   → venció sin renovar
 *   cancelled → cancelada manualmente
 */
class Subscription extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected $table = 'subscriptions';

    protected $fillable = [
        'account_id',
        'plan_id',
        'branch_id',
        'price_paid',
        'currency',
        'status',
        'starts_at',
        'ends_at',
        'cancelled_at',
    ];

    protected $casts = [
        'price_paid'   => 'float',
        'starts_at'    => 'datetime',
        'ends_at'      => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $event) => "Subscription #{$this->id} {$event}");
    }

    // =========================================================================
    // RELACIONES
    // =========================================================================

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Todos los cobros de esta subscripción.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Solo el último pago confirmado.
     */
    public function lastPayment(): BelongsTo
    {
        return $this->payments()->where('status', 'paid')->latest('paid_at')->first();
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeMonthly($query)
    {
        return $query->whereHas('plan', fn ($q) => $q->where('billing_cycle', 'monthly'));
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    public function isActive(): bool    { return $this->status === 'active'; }
    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isExpired(): bool   { return $this->status === 'expired'; }
    public function isCancelled(): bool { return $this->status === 'cancelled'; }

    public function isMonthly(): bool
    {
        return $this->plan?->billing_cycle === 'monthly';
    }

    public function isExpiredByDate(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }
}