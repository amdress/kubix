<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * BranchSolution
 *
 * Vincula una Branch con una Solution del catálogo global.
 * Es aquí donde el branch_manager habilita una solución en su territorio
 * y define el precio local (sobreescribiendo el default del Plan).
 *
 * EJEMPLO:
 *   Juan habilita Aluggap en Curitiba → crea BranchSolution
 *   Juan define pricing: { "monthly": 59.00 } → más caro que el default de R$49
 *   Empresas de Curitiba pagan R$59/mes por Aluggap
 *
 * PRECIO:
 *   pricing json → { "weekly": 25.00, "monthly": 80.00 }
 *   Si no hay precio definido aquí → se usa Plan.default_price
 */
class BranchSolution extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected $table = 'branch_solutions';

    protected $fillable = [
        'branch_id',
        'solution_id',
        'settings',
        'pricing',
        'is_active',
        'enabled_at',
        'disabled_at',
    ];

    protected $casts = [
        'settings'    => 'array',
        'pricing'     => 'array',
        'is_active'   => 'boolean',
        'enabled_at'  => 'datetime',
        'disabled_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(
                fn (string $event) => "BranchSolution {$event} — Branch #{$this->branch_id} Solution #{$this->solution_id}"
            );
    }

    // =========================================================================
    // RELACIONES
    // =========================================================================

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function solution(): BelongsTo
    {
        return $this->belongsTo(Solution::class);
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Retorna el precio local para un ciclo de facturación.
     * Si no está definido en el pricing local, retorna null
     * y el Plan.default_price se usa como fallback.
     *
     * @param string $cycle  weekly | monthly | yearly | once
     */
    public function getPriceFor(string $cycle): ?float
    {
        return $this->pricing[$cycle] ?? null;
    }
}