<?php

namespace App\Models;

use App\Models\Traits\HasDynamicGeometry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Address extends Model
{
    use SoftDeletes;
    use LogsActivity;
    use HasDynamicGeometry;

    protected $table = 'addresses';

    protected $fillable = [
        'addressable_type',
        'addressable_id',
        'label',
        'zip_code',
        'street',
        'number',
        'complement',
        'reference',
        'latitude',
        'longitude',
        'is_primary',
    ];

    protected $casts = [
        'latitude'   => 'float',
        'longitude'  => 'float',
        'is_primary' => 'boolean',
    ];

    /**
     * Configuración de auditoría.
     *
     * Se loguean todos los campos — una dirección es un dato sensible
     * y cualquier cambio debe quedar registrado.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(
                fn (string $event) => "Address {$event} para {$this->addressable_type} #{$this->addressable_id}"
            );
    }

    public function addressable(): MorphTo { return $this->morphTo(); }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([$this->street, $this->number, $this->complement]);
        return implode(', ', $parts);
    }
}