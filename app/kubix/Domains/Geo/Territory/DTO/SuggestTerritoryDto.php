<?php

namespace App\Kubix\Domains\Geo\Territory\DTO;

use Spatie\LaravelData\Data;

/**
 * SuggestTerritoryDto
 *
 * DTO para sugerencias de territorios (autocomplete).
 *
 * Recibe un texto libre ingresado por el administrador
 * (ej: "Batel") y será usado para consultar Nominatim.
 */
class SuggestTerritoryDto extends Data
{
    public function __construct(
        public string $query,
    ) {}

    public static function rules(): array
    {
        return [
            'query' => ['required', 'string', 'min:2', 'max:100'],
        ];
    }

    /**
     * Mensajes personalizados de validación
     */
    public static function messages(): array
    {
        return [
            'query.required' => 'Debes ingresar un término de búsqueda.',
            'query.string'   => 'El valor de búsqueda debe ser texto.',
            'query.min'      => 'El término debe tener al menos 2 caracteres.',
            'query.max'      => 'El término no puede superar los 100 caracteres.',
        ];
    }

    /**
     * Normalización básica del input
     */
    public function queryNormalized(): string
    {
        return trim($this->query);
    }
}