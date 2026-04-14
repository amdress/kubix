<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\DB;

class Territory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type', 'code', 'name', 'normalized_name', 'slug', 
        'parent_id', 'path', 'depth', 'boundary', 
        'latitude', 'longitude', 'is_active'
    ];

    protected $hidden = ['boundary'];

    /**
     * Definición de casts mediante método (Recomendado en Laravel 10/11)
     */
    protected function casts(): array
    {
        return [
            'depth'     => 'integer',
            'latitude'  => 'float',
            'longitude' => 'float',
            'is_active' => 'boolean',
        ];
    }

    // --- ATRIBUTOS (NUEVA SINTAXIS) ---

    /**
     * Transforma el binario WKB en GeoJSON.
     * Optimizamos evitando una consulta extra si es posible, 
     * aunque con tipos GEOMETRY en MySQL, ST_AsGeoJSON en el Query Builder es ideal.
     */
    protected function boundaryGeojson(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->exists || !($this->attributes['boundary'] ?? null)) {
                    return null;
                }
                
                // Nota: Si usas mucho este campo, considera añadir ST_AsGeoJSON 
                // directamente en el scope de consulta global o local.
                $result = DB::table('territories')
                    ->selectRaw('ST_AsGeoJSON(boundary) as geo')
                    ->where('id', $this->id)
                    ->first();

                return $result ? json_decode($result->geo) : null;
            }
        );
    }

    // --- RELACIONES ---

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    /**
 * =============================================================================
 * FUNCIONES PRINCIPALES: INTEGRIDAD JERÁRQUICA
 * =============================================================================
 * Controla el borrado en cascada basado en la estructura de Path Enumeration.
 */
protected static function booted(): void
{
    static::deleted(function (Territory $territory) {
        // Al usar SoftDeletes, esto marcará como borrados a todos los hijos
        // que contengan el path del padre como prefijo.
        $territory->where('path', 'like', "{$territory->path}%")->delete();
    });

    static::restored(function (Territory $territory) {
        // Opcional: Si restauras un padre, restauras a toda su descendencia
        $territory->where('path', 'like', "{$territory->path}%")->restore();
    });
}
}