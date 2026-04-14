<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Affiliation extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'affiliations';

    protected $fillable = [
        'user_id',
        'affiliatable_id',
        'affiliatable_type',
        'path',     // El "Mundo"
        'depth',    // El "Nivel"
        'role',     // El "Poder"
        'is_active',
        'assigned_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(
                fn (string $event) => "Affiliation {$event}: user #{$this->user_id} scope -> {$this->path} [{$this->role}]"
            );
    }

    // Relaciones
    public function user(): BelongsTo      { return $this->belongsTo(User::class); }
    public function affiliatable(): MorphTo { return $this->morphTo(); }
    public function assignedBy(): BelongsTo { return $this->belongsTo(User::class, 'assigned_by'); }

    // Helpers de Contexto
    public function isActive(): bool
    {
        return $this->is_active && is_null($this->deleted_at);
    }

    /**
     * Scope para filtrar recursos basados en este path.
     * Esto lo usaremos en el Middleware de Contexto.
     */
    public function scopeApplyScope($query, $builder)
    {
        return $builder->where('path', 'like', $this->path . '%');
    }
}