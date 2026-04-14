<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Account
 *
 * Instancia ACTIVA de una Solution operando en una Branch para una Company.
 *
 * RESPONSABILIDAD:
 *   Account dice QUÉ solución está activa para quién y dónde.
 *   Subscription dice CÓMO se paga esa solución.
 *
 * RELACIÓN:
 *   Company → Account → Solution (qué solución tiene activa)
 *           → Subscription → Plan (cómo paga)
 *                          → Payment (historial de cobros)
 *
 * Una Company puede tener múltiples Accounts (una por solución contratada).
 * Una Account puede tener múltiples Subscriptions (historial de renovaciones).
 */
class Account extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'accounts';

    protected $fillable = [
        'branch_id',
        'solution_id',
        'solution_slug',
        'accountable_id',
        'accountable_type',
        'branding',
        'status',
        'is_active',
        'activated_at',
        'suspended_at',
        'cancelled_at',
        'created_by',
    ];

    protected $casts = [
        'branding'     => 'array',
        'is_active'    => 'boolean',
        'activated_at' => 'datetime',
        'suspended_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $event) => "Account #{$this->id} {$event}");
    }

    // =========================================================================
    // RELACIONES — ESTRUCTURA
    // =========================================================================

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function solution(): BelongsTo
    {
        return $this->belongsTo(Solution::class);
    }

    /**
     * Compatibilidad con AccessMap del AuthService.
     */
    public function solutions(): BelongsTo
    {
        return $this->solution();
    }

    /**
     * Dueño del contrato — Company o User (polimórfico).
     */
    public function accountable(): MorphTo
    {
        return $this->morphTo();
    }

    public function affiliations(): MorphMany
    {
        return $this->morphMany(Affiliation::class, 'affiliatable');
    }

    // =========================================================================
    // RELACIONES — FINANCIERO
    // =========================================================================

    /**
     * Todas las subscripciones de esta Account.
     * Incluye activas, vencidas y canceladas (historial completo).
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Solo la subscripción activa actual.
     */
    public function activeSubscription()
    {
        return $this->subscriptions()->where('status', 'active')->latest()->first();
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    public function scopeForSolution($query, string $slug)
    {
        return $query->where('solution_slug', $slug);
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    public function isOperational(): bool
    {
        return $this->is_active && $this->status === 'active';
    }

    public function isSolution(string $slug): bool
    {
        return $this->solution_slug === $slug;
    }

    public function getBrandingData(): array
    {
        return $this->branding ?? [
            'primary_color' => '#1e3a8a',
            'logo'          => null,
            'watermark'     => null,
        ];
    }
}

