<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Str;

class Branch extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'telephone',
        'email',
        'is_physical',
        'branding',
        'territory_id',
        'territory_level',
        'territory_path',
        'is_active',
        'activated_at',
        'closed_at',
    ];

    protected $casts = [
        'is_physical'  => 'boolean',
        'branding'     => 'json',
        'is_active'    => 'boolean',
        'activated_at' => 'datetime',
        'closed_at'    => 'datetime',
    ];

    /**
     * EAGER LOADING AUTOMÁTICO
     * Cada vez que pidas una Branch, Laravel traerá el territorio y la dirección.
     * Esto hace que el "juego" que hicimos en Tinker sea el comportamiento estándar.
     */
    protected $with = ['territory', 'address'];

    /**
     * APPENDS AUTOMÁTICOS
     * Añade campos calculados al JSON que no existen en la DB.
     */
    protected $appends = ['is_online'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($branch) {
            if (empty($branch->slug)) {
                $branch->slug = Str::slug($branch->name);
            }
        });
    }

    // =========================================================================
    // RELACIONES (Strict typing para evitar errores)
    // =========================================================================

    public function territory(): BelongsTo
    {
        return $this->belongsTo(Territory::class, 'territory_id');
    }

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    // =========================================================================
    // ACCESSORS (Los "Grasas" que inflan el JSON)
    // =========================================================================

    /**
     * Atributo dinámico: Determina si la branch está operativa ahora mismo.
     */
    public function getIsOnlineAttribute(): bool
    {
        return $this->is_active && is_null($this->closed_at);
    }

    /**
     * Blindaje de Branding: Si el JSON de la DB es nulo o incompleto, 
     * array_merge asegura que el Frontend reciba los colores base de KUBIX.
     */
    public function getBrandingAttribute($value): array
    {
        $defaults = [
            'primary_color'      => '#3B82F6', 
            'secondary_color'    => '#1E293B',
            'font_family'        => 'Inter, sans-serif',
            'welcome_message'    => "Bem-vindo a " . $this->name,
            'show_announcements' => true,
        ];

        $current = is_string($value) ? json_decode($value, true) : ($value ?? []);
        
        return array_merge($defaults, (array)$current);
    }

    // =========================================================================
    // SCOPES & MEDIA
    // =========================================================================

    public function scopeActive($query) { return $query->where('is_active', true); }
    
    public function scopePhysical($query) { return $query->where('is_physical', true); }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logos')->singleFile();
        $this->addMediaCollection('watermarks')->singleFile();
        $this->addMediaCollection('splash_screens')->singleFile();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'is_physical', 'is_active', 'branding'])
            ->logOnlyDirty()
            ->useLogName('branch_management');
    }
}