<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Company
 *
 * Empresa registrada en Kubix dentro de un territorio (Branch).
 *
 * TIPOS:
 *   informal → sin CNPJ (vendedor ambulante, negocio familiar)
 *   formal   → con CNPJ registrado
 *
 * Una Company informal puede volverse formal cuando registre su CNPJ.
 * Las empresas formales tienen acceso a onboarding y soluciones premium.
 */
class Company extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected $table = 'companies';

    protected $fillable = [
        'branch_id',
        'name',
        'trade_name',
        'slug',
        'type',
        'cnpj',
        'email',
        'phone',
        'branding',
        'social_links',
        'is_verified',
        'verified_at',
        'is_active',
    ];

    protected $casts = [
        'branding'    => 'array',
        'social_links'=> 'array',
        'is_verified' => 'boolean',
        'is_active'   => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $event) => "Company {$this->name} {$event}");
    }

    // =========================================================================
    // RELACIONES — ESTRUCTURA
    // =========================================================================

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function affiliations(): MorphMany
    {
        return $this->morphMany(Affiliation::class, 'affiliatable');
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    // =========================================================================
    // RELACIONES — SOLUCIONES Y CONTRATOS
    // =========================================================================

    /**
     * Cuentas activas de la empresa (soluciones contratadas).
     */
    public function accounts(): MorphMany
    {
        return $this->morphMany(Account::class, 'accountable');
    }

    /**
     * Account activa de publicidad.
     */
    public function advertisingAccount()
    {
        return $this->accounts()
            ->where('solution_slug', 'advertising')
            ->where('is_active', true)
            ->first();
    }

    // =========================================================================
    // RELACIONES — FINANCIERO
    // =========================================================================

    /**
     * Todos los pagos realizados por esta empresa.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Pagos confirmados.
     */
    public function confirmedPayments(): HasMany
    {
        return $this->hasMany(Payment::class)->where('status', 'paid');
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFormal($query)
    {
        return $query->where('type', 'formal');
    }

    public function scopeInformal($query)
    {
        return $query->where('type', 'informal');
    }

    public function scopeInBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    public function isActive(): bool   { return $this->is_active; }
    public function isFormal(): bool   { return $this->type === 'formal'; }
    public function isInformal(): bool { return $this->type === 'informal'; }
    public function isVerified(): bool { return $this->is_verified; }

    public function displayName(): string
    {
        return $this->trade_name ?? $this->name;
    }

    /**
     * Cuánto ha pagado esta empresa en total.
     */
    public function totalPaid(): float
    {
        return $this->payments()->where('status', 'paid')->sum('amount');
    }
}