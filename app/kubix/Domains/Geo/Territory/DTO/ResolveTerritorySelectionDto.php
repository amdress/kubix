<?php

namespace App\Kubix\Domains\Geo\Territory\DTO;

class ResolveTerritorySelectionDto
{
    public function __construct(
        public string $display_name,
        public string $territory_type,
        public float $latitude,
        public float $longitude,
        public ?string $country = null,
        public ?string $state = null,
        public ?string $city = null,
        public ?string $neighborhood = null
    ) {}

    /**
     * Mapeo manual después de validar
     */
    public static function fromArray(array $data): self
    {
        return new self(
            display_name:   $data['display_name'],
            territory_type: $data['territory_type'],
            latitude:       (float) $data['latitude'],
            longitude:      (float) $data['longitude'],
            country:        $data['country'] ?? null,
            state:          $data['state'] ?? null,
            city:           $data['city'] ?? null,
            neighborhood:   $data['neighborhood'] ?? null
        );
    }

    /**
     * Reglas para el $request->validate()
     */
    public static function rules(): array
    {
        return [
            'display_name'   => 'required|string|max:255',
            'territory_type' => 'required|in:neighborhood,city,state,country',
            'latitude'       => 'required|numeric|between:-90,90',
            'longitude'      => 'required|numeric|between:-180,180',
            'country'        => 'nullable|string|max:255',
            'state'          => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:255',
            'neighborhood'   => 'nullable|string|max:255',
        ];
    }

    /**
     * Mensajes para el $request->validate()
     */
    public static function messages(): array
    {
        return [
            'display_name.required'   => 'El nombre de la ubicación es requerido.',
            'territory_type.required' => 'El tipo de territorio es obligatorio.',
            'territory_type.in'       => 'El tipo debe ser neighborhood, city, state o country.',
            'latitude.required'       => 'La latitud es necesaria para el mapa.',
            'latitude.between'        => 'La latitud está fuera de rango.',
            'longitude.required'      => 'La longitud es necesaria.',
            'longitude.between'       => 'La longitud está fuera de rango.',
        ];
    }
}