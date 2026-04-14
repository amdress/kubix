<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Representa o perfil comercial de um cliente final dentro de uma empresa.
 * Este modelo isola os dados de negócio da identidade global do usuário.
 */
class CustomerProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_id',
        'metadata',
        'status',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Identidade global do cliente.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Empresa proprietária deste registro de cliente (Multi-tenant).
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}