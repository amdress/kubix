<?php

namespace App\Kubix\Domains\Geo\Territory\DTO;

use Spatie\LaravelData\Data;

/**
 * CreateTerritoryDto
 *
 * DTO para la sección "territory" enviada desde el frontend.
 * Define la jerarquía territorial donde se creará la branch.
 *
 * Al menos "country" es requerido. Los demás son opcionales.
 * Si envías solo "country", se crea una branch a nivel país.
 * Si envías "country" + "state" + "city", se crea a nivel ciudad.
 */
class CreateTerritoryDto extends Data
{
    public function __construct(
        public ?string $country      = null,
        public ?string $state        = null,
        public ?string $city         = null,
        public ?string $neighborhood = null,
    ) {}

    public static function rules(): array
    {
        return [
            'country'      => ['required', 'string', 'max:100'],
            'state'        => ['nullable', 'string', 'max:100'],
            'city'         => ['nullable', 'string', 'max:100'],
            'neighborhood' => ['nullable', 'string', 'max:100'],
        ];
    }
}