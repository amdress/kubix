<?php

namespace App\Models;

use App\Models\Affiliation;
use App\Models\DeviceToken;
use App\Models\Traits\HasAddresses;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail, HasMedia
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use SoftDeletes;
    use InteractsWithMedia;
    use HasAddresses;
    use LogsActivity;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'phone',
        'terms_accepted_at',
        'terms_version',
        'identity_verified',
        'identity_verified_at',
        'last_login_at',
        'last_login_ip',
        'password_changed_at',
        'blocked_at',
        'registered_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at'    => 'datetime',
        'password'             => 'hashed',
        'terms_accepted_at'    => 'datetime',
        'identity_verified'    => 'boolean',
        'identity_verified_at' => 'datetime',
        'last_login_at'        => 'datetime',
        'password_changed_at'  => 'datetime',
        'blocked_at'           => 'datetime',
    ];

    /**
     * Configuración de auditoría.
     *
     * password, last_login_at, last_login_ip excluidos:
     *   - password nunca en logs
     *   - logins generarían un registro en cada acceso
     *
     * terms_accepted_at e identity_verified_at SÍ se loguean —
     * son evidencia legal crítica que nunca debe perderse.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logExcept(['password', 'last_login_at', 'last_login_ip', 'remember_token'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(
                fn (string $event) => "User {$event}: {$this->name} ({$this->email})"
            );
    }

    /* -------------------------------------------------------------------------- */
    /* RELACIONES                                                                  */
    /* -------------------------------------------------------------------------- */

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function deviceTokens(): HasMany
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function affiliations(): HasMany
    {
        return $this->hasMany(Affiliation::class);
    }

    /* -------------------------------------------------------------------------- */
    /* MEDIA (Spatie)                                                              */
    /* -------------------------------------------------------------------------- */

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatars')
            ->singleFile();

        /**
         * Documento de identidad para KYC.
         * Foto del CPF o cédula de identidad del usuario.
         * Requerido para verificar la identidad del representante
         * al registrar una Company o contratar ciertas soluciones.
         */
        $this->addMediaCollection('identity_document')
            ->singleFile();

        $this->addMediaCollection('documents');
    }

    /* -------------------------------------------------------------------------- */
    /* HELPERS                                                                     */
    /* -------------------------------------------------------------------------- */

    public function hasAcceptedTerms(): bool
    {
        return ! is_null($this->terms_accepted_at);
    }

    public function isIdentityVerified(): bool
    {
        return $this->identity_verified;
    }

    public function isBlocked(): bool
    {
        return ! is_null($this->blocked_at);
    }
}