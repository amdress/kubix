<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Solution
 *
 * Catálogo global de mini-apps disponibles en Kubix.
 * El superadmin las registra aquí.
 * Los branch_managers las habilitan en su territorio via BranchSolution.
 *
 * TIPOS:
 *   core     → vitales para la plataforma (ej: Publicidad Territorial)
 *   addon    → extras opcionales
 *   vertical → mercados específicos (Aluggap → inmobiliaria, LibreJuros → finanzas)
 *
 * EXTENSIBILIDAD:
 *   Cualquier dev puede proponer una nueva Solution.
 *   Si es aceptada, se registra aquí y el código vive en solutions/{slug}/
 */
class Solution extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected $table = 'solutions';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'domain',
        'base_git_branch',
        'features',
        'config_schema',
        'is_active',
        'is_public',
    ];

    protected $casts = [
        'features'      => 'array',
        'config_schema' => 'array',
        'is_active'     => 'boolean',
        'is_public'     => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $event) => "Solution {$this->name} {$event}");
    }

    // =========================================================================
    // RELACIONES
    // =========================================================================

    /**
     * Branches donde esta Solution está habilitada.
     */
    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'branch_solutions')
            ->withPivot(['pricing', 'settings', 'is_active', 'enabled_at'])
            ->withTimestamps()
            ->wherePivot('is_active', true);
    }

    /**
     * Registros de activación por Branch (con pricing local).
     */
    public function branchSolutions(): HasMany
    {
        return $this->hasMany(BranchSolution::class);
    }

    /**
     * Planes de pago asociados a esta Solution.
     */
    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class);
    }

    /**
     * Accounts activas usando esta Solution.
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeCore($query)
    {
        return $query->where('type', 'core');
    }

    public function scopeVertical($query)
    {
        return $query->where('type', 'vertical');
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    public function isActive(): bool  { return $this->is_active; }
    public function isPublic(): bool  { return $this->is_public; }
    public function isCore(): bool    { return $this->type === 'core'; }
    public function isVertical(): bool { return $this->type === 'vertical'; }

    /**
     * Cuántas Branches tienen esta Solution activa.
     */
    public function activeBranchCount(): int
    {
        return $this->branchSolutions()->where('is_active', true)->count();
    }
}