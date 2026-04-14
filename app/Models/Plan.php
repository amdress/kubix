<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Plan
 *
 * Catálogo global de planes de pago disponibles en Kubix.
 * Define QUÉ se ofrece, CUÁNTO cuesta por defecto y CADA CUÁNTO se cobra.
 *
 * El precio default puede sobreescribirse por territorio via branch_solutions.pricing
 *
 * TIPOS:
 *   advertising → publicidad territorial (default para todas las empresas)
 *   onboarding  → personalización para empresas formales (pago único)
 *   solution    → soluciones específicas (Aluggap, LibreJuros, etc.)
 *   franchise   → membresía de franquiciado (futuro)
 *
 * CICLOS:
 *   weekly  → semanal
 *   monthly → mensual
 *   yearly  → anual
 *   once    → pago único
 */
class Plan extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected $table = 'plans';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'billing_cycle',
        'default_price',
        'currency',
        'solution_id',
        'is_active',
    ];

    protected $casts = [
        'default_price' => 'float',
        'is_active'     => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $event) => "Plan {$this->name} {$event}");
    }

    // =========================================================================
    // RELACIONES
    // =========================================================================

    /**
     * Solution a la que pertenece este plan (opcional).
     * Ej: Plan "Aluggap Mensual" → Solution "aluggap"
     */
    public function solution(): BelongsTo
    {
        return $this->belongsTo(Solution::class);
    }

    /**
     * Todas las subscripciones creadas con este plan.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeAdvertising($query)
    {
        return $query->where('type', 'advertising');
    }

    public function scopeSolutions($query)
    {
        return $query->where('type', 'solution');
    }

    public function scopeMonthly($query)
    {
        return $query->where('billing_cycle', 'monthly');
    }

    public function scopeWeekly($query)
    {
        return $query->where('billing_cycle', 'weekly');
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    public function isAdvertising(): bool { return $this->type === 'advertising'; }
    public function isOnboarding(): bool  { return $this->type === 'onboarding'; }
    public function isSolution(): bool    { return $this->type === 'solution'; }
    public function isFranchise(): bool   { return $this->type === 'franchise'; }

    public function isMonthly(): bool     { return $this->billing_cycle === 'monthly'; }
    public function isWeekly(): bool      { return $this->billing_cycle === 'weekly'; }
    public function isOnce(): bool        { return $this->billing_cycle === 'once'; }
}